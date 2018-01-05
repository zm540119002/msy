<?php
namespace Mall\Controller;

use  web\all\Controller\BaseController;
use  Mall\Controller\WxPayController;

class IndexController extends BaseController{
    //商城-首页
    public function index(){
        //购物车配置开启的项
        $this->unlockingFooterCartSingle = unlockingFooterCartConfig(array(2,3,4));
        $this->display();
    }

    //微团购-首页
    public function groupBuyIndex(){
        $this->display('GroupBuy/index');
    }
}
