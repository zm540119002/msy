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
                    'o.id', 'o.sn', 'o.amount','o.actually_amount',
                    'o.user_id','o.type'
                ],
            ];
            $orderInfo = $modelOrder->getInfo($config);
            $payInfo = [
                'sn'=>$orderInfo['sn'],
                'actually_amount'=>$orderInfo['actually_amount'],
                'cancel_back' => "http://".$_SERVER['HTTP_HOST'].url('payCancel'),
                'fail_back' => "http://".$_SERVER['HTTP_HOST'].url('payFail'),
                'success_back' => "http://".$_SERVER['HTTP_HOST'].url('payComplete'),
                'notify_url'=>"http://".$_SERVER['HTTP_HOST']."/purchase/".config('wx_config.call_back_url')

            ];
            $payCode = input('pay_code','0','int');
            //微信支付
            if($payCode == 1){
                $payInfo['notify_url'] = $payInfo['notify_url'].'/weixin.order';
                \common\component\payment\weixin\weixinpay::wxPay($payInfo);
            }
            //支付宝支付
            if($payCode == 2){
                $payInfo['notify_url'] = $payInfo['notify_url'].'/ali.order';
                $model = new \common\component\payment\alipay\alipay;
                return $model->get_code($payInfo);
            }
            //银联支付
            if($payCode == 3){
                $payInfo['notify_url'] = $payInfo['notify_url'].'/union.order';
                $model = new \common\component\payment\unionpay\unionpay;
                return $model->get_code($payInfo);
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