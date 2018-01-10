<?php
namespace Home\Controller;

use web\all\Cache\PartnerCache;
use web\all\Controller\AuthUserController;

class PartnerAuthoriseController extends AuthUserController {
    //合伙人认证-首页
    public function index(){
        if(IS_POST){
        }else{
            if($this->partner['auth_status'] == 2){//已认证
                $this->display('authorizeComplete');
            }else{//未认证
                //购物车配置开启的项
                $this->unlockingFooterCart = unlockingFooterCartConfig(array(14));
                $this->display();
            }
        }
    }

    //城市查找
    public function citySearch(){
        if(IS_POST){
        }else{
            //购物车配置开启的项
            $this->unlockingFooterCart = unlockingFooterCartConfig(array(15));
            $this->display();
        }
    }

    //登记资料
    public function register(){
        $modelPartner = D('Partner');
        if(IS_POST){
            $rules = array(
                array('name','require','姓名必须！'),
                array('mobile_phone','isMobile','请输入正确的手机号码',0,'function'),
            );
            if(isset($_POST['partnerId']) && intval($_POST['partnerId'])){//修改
                $where = array(
                    'user_id' => $this->user['id'],
                );
                $res = $modelPartner->savePartner($where,$rules);
                PartnerCache::remove($this->user['id']);
            }else{//新增
                $_POST['user_id'] = $this->user['id'];
                $_POST['create_time'] = time();
                $res = $modelPartner->addPartner($rules);
            }
            $this->ajaxReturn($res);
        }else{
            //购物车配置开启的项
            $this->unlockingFooterCart = unlockingFooterCartConfig(array(16));
            $partner = PartnerCache::get($this->user['id']);
            $this->assign('partner',$partner);
            $this->display();
        }
    }

    //席位订金
    public function seatDeposit(){
        if(IS_POST){
        }else{
            $this->display();
        }
    }

    //资格完款
    public function seniority(){
        if(IS_POST){
        }else{
            $this->display();
        }
    }
}