<?php
namespace Business\Controller;

use web\all\Controller\AuthPartnerController;
use web\all\Cache\PartnerCache;

class PartnerController extends AuthPartnerController {
    //城市合伙人-首页
    public function index(){
        //用户信息
        $this->assign('user',$this->user);
        //购物车配置开启的项
        $this->unlockingFooterCart = unlockingFooterCartConfig(array(1,2,5));
        $this->display();
    }

    //完善我的商务档案
    public function completeArchive(){
        if(IS_POST){
        }else{
            //购物车配置开启的项
            $this->unlockingFooterCart = unlockingFooterCartConfig(array(21));
            //合伙人信息
            $this->partnerInfo = PartnerCache::get($this->user['id']);
            //省市数组
            $this->assign('provinceList',getProvinceCity());
            $this->display();
        }
    }

    //授权我的代理商
    public function authoriseAgent(){
        if(IS_POST){
        }else{
            echo date('Y-m-d H:i:s','1516010749');
            echo "\n";
            echo date('Y-m-d H:i:s','1516066425');
            echo "\n";
            echo date('Y-m-d H:i:s','1516010777');
            echo "\n";
            echo date('Y-m-d H:i:s','1516066463');
            echo "\n";
            echo date('Y-m-d H:i:s','1516091901');
            echo "\n";
            echo date('Y-m-d H:i:s','1516163740');
            echo "\n";
            exit;
            //购物车配置开启的项
            $this->unlockingFooterCart = unlockingFooterCartConfig(array(22));
            //合伙人信息
            $this->partnerInfo = PartnerCache::get($this->user['id']);
            $this->display();
        }
    }

    //查看我的代理商
    public function viewAgent(){
        if(IS_POST){
        }else{
            $this->display();
        }
    }
}