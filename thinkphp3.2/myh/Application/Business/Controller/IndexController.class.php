<?php
namespace Business\Controller;

use  web\all\Controller\BaseController;

class IndexController extends BaseController{
    //商务-首页
    public function index(){
        //购物车配置开启的项
        $this->unlockingFooterCart = unlockingFooterCartConfig(array(13));
        $this->display();
    }

    //代理商-首页
    public function agentIndex(){
        //用户信息
        $this->assign('user',$this->user);
        //购物车配置开启的项
        $this->unlockingFooterCart = unlockingFooterCartConfig(array(1,2,5));
        $this->agentType = I('get.agentType',0,'int');
        if($this->agentType==1){//实体店代理商
        }elseif ($this->agentType==2){//微商代理商
        }elseif ($this->agentType==3){//礼品采购代理商
        }
        $this->display();
    }

    //合伙人-首页
    public function partnerIndex(){
        //用户信息
        $this->assign('user',$this->user);
        //购物车配置开启的项
        $this->unlockingFooterCart = unlockingFooterCartConfig(array(1,2,5));
        $this->display();
    }
}
