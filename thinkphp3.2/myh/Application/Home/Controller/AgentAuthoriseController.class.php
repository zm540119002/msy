<?php
namespace Home\Controller;

use web\all\Cache\CompanyCache;
use web\all\Controller\AuthUserController;

class AgentAuthoriseController extends AuthUserController {
    //礼品采购代理商-认证
    public function index(){
        if(IS_POST){
        }else{
            //购物车配置开启的项
            $this->unlockingFooterCart = unlockingFooterCartConfig(array(19));
            $this->display();
        }
    }
}