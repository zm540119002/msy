<?php
namespace app\index\controller;

class Wallet extends Base {

    public function __construct(){
        parent::__construct();

        // 判断是否已开通钱包,后面改进此方法
        if( in_array(request()->action(),['index']) ){
            if(empty($this->wallet['password'])){
                // 开通钱包
                $this->assign('user',$this->user);
                echo $this->fetch('wallet_opening');
                exit;
            }

        }

    }

    /**
     * 钱包详情页面
     */
    public function index(){

        if (request()->isAjax()) {
        } else {

            $model = new \app\index\model\Wallet();
            $condition = [
                'where' => [
                    ['user_id','=',$this->user['id']],
                    ['status','=',0]
                ],
                'field' => [
                    'id','user_id','status','amount','password','salt'
                ],
            ];

            $wallet= $model->getInfo($condition);
            $this->assign('wallet',$wallet);

            return $this->fetch('recharge');
        }
    }

    /**
     * 充值支付 -生成充值订单,再处理各支付方式的业务 ajax
     */
    public function rechargeOrder(){

        if(!request()->isAjax()){
            return errorMsg('参数错误');
        }

        $amount = input('amount/f');
        $payCode= input('pay_code/d');

        if( !$amount || !$payCode ){
            return errorMsg('参数错误');
        }

        //生成充值明细
        $walletDetailSn = generateSN();
        $data = [
            'sn'=>$walletDetailSn,
            'user_id'=>$this->user['id'],
            'amount'=>$amount,
            'actually_amount'=>$amount, // 还没有其它的业务 暂时先用$amount
            'create_time'=>time(),
            'payment_code'=>$payCode,
        ];

        // 线下汇款凭证
        if( isset($_POST['voucher']) && $_POST['voucher'] ){
            $data['voucher_img'] = moveImgFromTemp(config('upload_dir.scheme'),$_POST['voucher']);
        }

        $model= new \app\index\model\WalletDetail();
        $res  = $model->isUpdate(false)->save($data);
        if(!$res){
            return errorMsg('充值失败');

        }

        // 各充值方式的处理
        switch($payCode){
            case config('custom.recharge_code.WeChatPay.code') :
            case config('custom.recharge_code.Alipay.code') :
            case config('custom.recharge_code.UnionPay.code') :
                $url = config('custom.pay_recharge').$walletDetailSn;
                return successMsg($url);
                break;

            case config('custom.recharge_code.OfflinePay.code') :
                // 更新状态
                $model->edit(['recharge_status'=>1],['sn'=>$walletDetailSn]);
                return successMsg('成功');
                break;
        }
    }

    /**
     * 充值记录页
     */
    public function rechargeDetail(){
        if (request()->isAjax()) {
        } else {
            return $this->fetch();
        }
    }
    
    /**
     * 设置||重置支付密码
     */
    public function forgetPassword(){

        if (request()->isAjax()) {
            $model = new \app\index\model\Wallet();;
            $postData = input('post.');
            $postData['user_id'] = $this->user['id'];
            return $model->resetPassword($postData);

        } else {
            return $this->fetch();
        }
    }

    /**
     * 钱包明细页
     */
    public function detailList(){

        $condition = [
            'where'=>[
                ['status','=',0],
                ['user_id','=',$this->user['id']],
            ],
            'field'=>[
                'id','sn','type','recharge_status','amount','payment_code','create_time','payment_time',
            ],
            'order'=>[
                'create_time'=>'desc'
            ]
        ];

        if( $type=input('type/d') )  $condition['where'][] = ['type','=',$type];

        $model = new \app\index\model\WalletDetail();
        $list = $model->pageQuery($condition);

        $this->assign('list',$list);
        return $this->fetch('list_tpl');
    }

    /**
     * 钱包支付处理
     * 验证用户，更新余额,更新订单信息，清除购物车商品，
     * ajax
     */
    public function checkWallet(){
        if (request()->isAjax()) {
            $password = input('password/s');
            $user_id  = $this->user['id'];
            if(empty($password)){
                return errorMsg('密码格式错误!');
            }

            $modelWallet = new \app\index\model\Wallet();;

            $wallet = $modelWallet->checkWalletUser($user_id,$password);
            if(!$wallet){
                return errorMsg('密码有误，请重新输入!');
            }

            // 更新订单状态并清除订单里购物车里的商品
            $fatherOrderId = input('post.order_id',0,'int');
            if(!$fatherOrderId){
                return errorMsg('订单支付失败!');
            }

            $modelOrder = new \app\index\model\Order();
            $condition = [
                'where' => [
                    ['user_id','=',$user_id],
                    ['id','=',$fatherOrderId],
                    ['order_status','<',2],
                ]
            ];

            $orderInfo  = $modelOrder->getInfo($condition);
            if(!$orderInfo){
                return errorMsg('订单已支付',['code'=>1]);
            }

            if($orderInfo['actually_amount']>$wallet['amount']){
                return errorMsg('钱包余额不足，请选择其它的支付方式!',['code'=>2]);
            }

            // 生成订单明细&&更新钱包余额
            $modelOrder ->startTrans();
            $modelWalletDetail = new \app\index\model\WalletDetail();
            $orderInfo['pay_sn'] = generateSN();
            $orderInfo['payment_time'] = time();
            $res = $modelWalletDetail->walletPaymentHandle($orderInfo);
            if(!$res['status'] ){
                $modelOrder->rollback();
                //返回状态
                return errorMsg('失败');
            }
            // 更新订单信息
            $data = input('post.');
            $data['payment_code'] = $data['pay_code'];
            $data['pay_sn'] = $orderInfo['pay_sn'];
            $data['payment_time'] = time();
            $data['order_sn'] = $orderInfo['sn'];

            $res = $modelOrder->orderHandle($data, $orderInfo);
            if(!$res['status']){
                $modelOrder->rollback();
                //返回状态
                return errorMsg('失败');
            }

            // 删除订单关联的购物车的商品
            if(false !== $res){
                $modelCart = new \app\index\model\Cart();
                $res = $modelCart->clearCartGoodsByOrder($fatherOrderId,$user_id);
            }

            if(false === $res){
                $modelOrder ->rollback();
                return errorMsg('失败');
            }

            $modelOrder -> commit();

            $url = url('Order/manage',['order_status'=>2],true,true);
            return successMsg('支付成功',['url' =>$url]);

        } else {
            return $this->fetch();
        }
    }


}