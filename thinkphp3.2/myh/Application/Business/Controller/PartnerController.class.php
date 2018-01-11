<?php
namespace Business\Controller;

use web\all\Controller\AuthPartnerController;

class PartnerController extends AuthPartnerController {
    //城市合伙人-首页
    public function index(){
        //用户信息
        $this->assign('user',$this->user);
        //购物车配置开启的项
        $this->unlockingFooterCart = unlockingFooterCartConfig(array(1,2,5));
        $this->display();
    }
}