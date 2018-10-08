<?php
namespace app\mall\controller;
class Index extends MallBase{

    public function index(){
        
        return $this->fetch();
    }

    public function getPerson()
    {
        return $this->fetch();
    }

    public function set()
    {
        return $this->fetch();
    }

    public function pay(){
        //微信支付
        $payInfo = array(
            'sn'=>generateSN(),
            'actually_amount'=>0.01,
            'cancel_back' => url('payCancel'),
            'fail_back' => url('payFail'),
            'success_back' => url('payComplete'),
            'notify_url'=>config('wx_config.call_back_url'),
        );
    \common\lib\Pay::wxPay($payInfo);
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

}