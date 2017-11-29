<?php
namespace Purchase\Controller;

use web\all\Controller\BaseController;
use web\all\Cache\CompanyCache;
use web\all\Lib\AuthUser;

class PersonalCenterController extends BaseController {
    //个人中心-首页
    public function index(){
        if(IS_POST){
        }else{
            //用户信息
            $this->user = AuthUser::check();
            if($this->user['id']){
                $this->company = CompanyCache::get($this->user['id']);
            }
            $this->display();
        }
    }

    //个人中心-采购订单
    public function purchaseOrder(){
        if(IS_POST){
        }else{
            //用户信息
            $this->user = AuthUser::check();
            if($this->user['id']){
                $this->company = CompanyCache::get($this->user['id']);
            }
            $this->display();       
        }
    }

    //个人中心-升级VIP
    public function upgrade(){
        $modeLevel = D('Level');
        if(IS_POST){
        }else{
            $where = array(
                'l.type' => 1,
            );
            $this->levelList = $modeLevel->selectLevel($where);
            $this->display();
        }
    }

    //个人中心-升级VIP-级别详情
    public function levelDetail(){
        $modeLevel = D('Level');
        if(IS_POST){
        }else{
            $id = I('get.levelId',0,'int');
            $where = array(
                'l.type' => 1,
                'l.id' => $id,
            );
            $levelInfo = $modeLevel->selectLevel($where);
            $this->levelInfo = $levelInfo[0];
            $this->display();
        }
    }
}