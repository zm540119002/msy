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
            $agentType = I('get.agentType',0,'int');
            if($agentType==1){//实体店
                $this->display('entityShop');
            }elseif ($agentType==2){//微商代理商
                $this->display('wechatBusiness');
            }elseif ($agentType==3){//礼品采购商
                $this->display('giftPurchase');
            }
        }
    }
}