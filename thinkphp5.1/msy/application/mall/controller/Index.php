<?php
namespace app\mall\controller;
use  common\lib\Pay;
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
        $payInfo = array(
            'sn'=>25120,
            'actually_amount'=>1,
            'cancel_back' => url('payCancel'),
            'fail_back' => url('payFail'),
            'success_back' => url('payComplete'),
            'notify_url'=>config('wx_config.CALL_BACK_URL'),
        );
        Pay::wxPay($payInfo);
    }

}