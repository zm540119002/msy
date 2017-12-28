<?php
namespace Mall\Controller;

use  web\all\Controller\BaseController;
use  web\all\Lib\Pay;
use  Mall\Controller\WxPayController;

class IndexController extends BaseController{
    //商城-首页
    public function index(){
        //购物车配置开启的项
        $this->unlockingFooterCartSingle = unlockingFooterCartConfig(array(2,3,4));
        $this->display();
    }

    //推客分享首页
    public function referrerGoodsIndex(){
        $this->display();
    }

    //支付测试
    public function payTs(){
        $order = array(
            'sn' => generateSN(),
            'actually_amount' => 0.01,
            'create_time'=>time(),
            'notify_url'=>SITE_URL.U('CallBack/notifyUrl',array('pay_code'=>'weixin.order'))
        );
        Pay::wxPay($order);
    }
}
