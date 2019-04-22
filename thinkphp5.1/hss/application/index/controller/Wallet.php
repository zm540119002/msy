<?php
namespace app\index\controller;

class Wallet extends Base {

    public function __construct(){
        parent::__construct();

        // 判断是否已开通钱包,后面改进此方法
        if( in_array(request()->action(),['recharge']) ){
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
     * 钱包充值页面
     */
    public function recharge(){

        if (request()->isAjax()) {
        } else {

            //$url = config('custom.pay_center');

            //return $this->redirect($url.request()->controller().'/'.request()->action());
        return $this->fetch();
        }
    }

    /**
     * 充值支付 -生成充值订单，跳转到支付页
     */
    public function rechargeOrder(){

        $amount = input('amount/f');
        $payCode= input('pay_code/d');

        if( !$amount || !$payCode ){
            $this -> error('参数错误');
        }

        //生成充值明细
        $WalletDetailSn = generateSN();
        $data = [
            'sn'=>$WalletDetailSn,
            'user_id'=>$this->user['id'],
            'amount'=>$amount,
            'create_time'=>time(),
            'payment_code'=>$payCode,
        ];
        $model= new \app\index\model\WalletDetail();
        $res  = $model->isUpdate(false)->save($data);
        if(!$res){
            $this -> error('充值失败');
        }

        $url = config('custom.pay_recharge');

        $this->redirect('index/Payment/rechargePay',['system_id'=>3,'order_sn'=>$WalletDetailSn]);

        //return $this->redirect('Payment/rechargePay/system_id/3/order_sn/'.$WalletDetailSn);
        //return $this->redirect($url.$WalletDetailSn);


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
                'id','sn','type','recharge_status','amount','payment_code','payment_time',
            ],
        ];

        $type = input('type/d');
        if($type)  $condition['where'][] = ['type','=',$type];

        $model = new \app\index\model\WalletDetail();
        $data = $model->getList($condition);
        $this->assign('data',$data);
    }



}