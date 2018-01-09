<?php
namespace Business\Controller;

use web\all\Controller\AuthAgentController;

class AgentController extends AuthAgentController {
    //代理商-首页
    public function index(){
        //用户信息
        $this->assign('user',$this->user);
        //购物车配置开启的项
        $this->unlockingFooterCart = unlockingFooterCartConfig(array(1,2,5));
        $this->display();
    }
}