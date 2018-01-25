<?php
namespace Business\Controller;

use web\all\Controller\BaseController;
use web\all\Lib\AuthUser;
use web\all\Cache\AgentCache;
use web\all\Cache\PartnerCache;

class IndexController extends BaseController{
    //商务-首页
    public function index(){
        //购物车配置开启的项
        $this->unlockingFooterCart = unlockingFooterCartConfig(array(13));
        $this->display();
    }

    //代理商-首页
    public function agentIndex(){
        //判断是否登录
        $this->user = AuthUser::check();
        if($this->user){
            $this->assign('user',$this->user);
            AgentCache::remove($this->user['id']);
            $this->agent = AgentCache::get($this->user['id']);
            if(!$this->agent){
                AgentCache::removeByMobilePhone($this->user['mobile_phone']);
                $this->agent = AgentCache::getByMobilePhone($this->user['mobile_phone']);
                if($this->agent){
                    //购物车配置开启的项
                    $this->unlockingFooterCart = unlockingFooterCartConfig(array(1,2,24));
                    $this->goodsListOptionType = 'purchaserOnly';
                }
            }
        }
        $this->agentType = I('get.agentType',0,'int');
        if($this->agentType==1){//实体店代理商
        }elseif ($this->agentType==2){//微商代理商
        }elseif ($this->agentType==3){//礼品采购代理商
        }
        $this->display();
    }

    //合伙人-首页
    public function partnerIndex(){
        //判断是否登录
        $this->user = AuthUser::check();
        if($this->user){
            $this->assign('user',$this->user);
            $this->partner = PartnerCache::get($this->user['id']);
            if($this->partner){
                //购物车配置开启的项
                $this->unlockingFooterCart = unlockingFooterCartConfig(array(1,2,24));
            }
        }
        $this->display();
    }
}
