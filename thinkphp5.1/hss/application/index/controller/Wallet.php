<?php
namespace app\index\controller;

class Wallet extends Base {

    public function __construct(){
        parent::__construct();

        // 判断是否已开通钱包,后面改进此方法
        if( in_array(request()->action(),['index']) ){
            if(empty($this->wallet['password'])){
                $this->redirect('walletOpening');
                exit;
            }

            $this->assign('wallet',$this->wallet);
        }

    }

    /**
     * 开通钱包页
     */
    public function walletOpening(){
        $user = session('user');
        $this->assign('user',$user);

        return $this->fetch();
    }

    /**
     * 钱包详情页面
     */
    public function index(){

        if (request()->isAjax()) {
        } else {

            //$url = config('custom.pay_center');

            //return $this->redirect($url.request()->controller().'/'.request()->action());
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

        // 各付款方式的处理
        switch($payCode){
            case config('custom.pay_code.WeChatPay.code') :

                $url = config('custom.pay_recharge').$walletDetailSn;
                return successMsg($url);
                return successMsg(request()->domain().url('/index/Payment/rechargePay', ['system_id'=>3,'order_sn'=>$walletDetailSn]));

                break;
            case config('custom.pay_code.Alipay.code') :
                break;
            case config('custom.pay_code.UnionPay.code') :
                break;
            case config('custom.pay_code.OfflinePay.code') :
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



}