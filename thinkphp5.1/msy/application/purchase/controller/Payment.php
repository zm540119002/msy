<?php
namespace app\purchase\controller;

class Payment extends \common\controller\UserBase{
    //订单-支付
    public function orderPayment(){

        if( !empty(input('sn')) && !empty(input('?pay_code'))){
            $modelOrder = new \app\purchase\model\Order();
            $orderSn = input('sn','','string');
            $config = [
                'where' => [
                    ['o.status', '=', 0],
                    ['o.sn', '=', $orderSn],
                    ['o.user_id', '=', $this->user['id']],
                ],'field' => [
                    'o.id', 'o.sn', 'o.amount',
                    'o.user_id','o.type'
                ],
            ];
            $orderInfo = $modelOrder->getInfo($config);
            $payInfo = [
                'sn'=>$orderInfo['sn'],
                'actually_amount'=>$orderInfo['amount'],
                'cancel_back' => url('payCancel'),
                'fail_back' => url('payFail'),
                'success_back' => url('payComplete'),
                'notify_url'=>"http://".$_SERVER['HTTP_HOST']."/purchase/".config('wx_config.call_back_url') .
                    ($orderInfo['type'] == 0?'/weixin.order':'/weixin.group_buy'),
            ];
            $payCode = input('pay_code','0','int');

            if($payCode == 1){
                \common\component\payment\weixin\weixinpay::wxPay($payInfo);
            }

            
            if($payCode == 2){
                $model = new \common\component\payment\alipayMobile\alipayMobile;
                return $model->get_code($payInfo);
            }
            if($payCode == 3){
                $model = new \common\component\payment\unionpay\unionpay;
                return $model->get_code($payInfo);
            }

        }


//        //微信支付
//        $payInfo = array(
//            'sn'=>generateSN(12),
//            'actually_amount'=>0.01,
//            'cancel_back' => url('payCancel'),
//            'fail_back' => url('payFail'),
//            'success_back' => url('payComplete'),
//            'notify_url'=>config('wx_config.call_back_url')
//        );
//        \common\component\payment\weixin\weixinpay::wxPay($payInfo);

        //支付宝支付
//        $order = [
//            'sn'=>generateSN(10),
//            'actually_amount'=>0.01,
//        ];
//        $model = new \common\component\payment\alipayMobile\alipayMobile;
//        return $model->get_code($order);
        //银联支付
//        $order = [
//            'sn'=>generateSN(),
//            'actually_amount'=>0.01,
//        ];
//        $model = new \common\component\payment\unionpay\unionpay;
//        return $model->get_code($order);
    }

    //充值-支付
    public function rechargePayment(){
        $modelWalletDetail = D('WalletDetail');
        if(IS_POST){
        }else{
            if(isset($_GET['walletDetailId']) && intval($_GET['walletDetailId'])){
                $where = array(
                    'wd.id' => I('get.walletDetailId'),
                    'wd.user_id' => $this->user['id'],
                );
                $walletDetailInfo = $modelWalletDetail->selectWalletDetail($where);
                $walletDetailInfo = $walletDetailInfo[0];
                $this->amount = $walletDetailInfo['amount'];
                $payInfo = array(
                    'sn'=>$walletDetailInfo['sn'],
                    'actually_amount'=>$this->amount,
                    'cancel_back' => U('payCancel'),
                    'fail_back' => U('payFail'),
                    'success_back' => U('payComplete'),
                    'notify_url'=>C('WX_CONFIG')['CALL_BACK_URL'].'/weixin.recharge',
                );
                Pay::wxPay($payInfo);
            }
        }
    }

    public function payComplete(){
        return $this->fetch();
    }
    public function payCancel(){
        return $this->fetch();
    }
    public function payFail(){
        return $this->fetch();
    }
}