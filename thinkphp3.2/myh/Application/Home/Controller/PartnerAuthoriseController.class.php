<?php
namespace Home\Controller;

use web\all\Cache\PartnerCache;
use web\all\Controller\AuthUserController;

class PartnerAuthoriseController extends AuthUserController {
    //合伙人认证-首页
    public function index(){
        if(IS_POST){
        }else{
            //购物车配置开启的项
            $this->unlockingFooterCart = unlockingFooterCartConfig(array(14));
            $this->display();
        }
    }

    //城市查找
    public function citySearch(){
        if(IS_POST){
        }else{
            //购物车配置开启的项
            $this->unlockingFooterCart = unlockingFooterCartConfig(array(15));
            //省市数组
            $this->assign('provinceList',getProvinceCity());
            $this->display();
        }
    }

    //登记资料
    public function register(){
        $modelPartner = D('Business/Partner');
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
                $_POST['auth_status'] = 1;
                $res = $modelPartner->addPartner($rules);
            }
            $this->ajaxReturn($res);
        }else{
            //购物车配置开启的项
            $this->unlockingFooterCart = unlockingFooterCartConfig(array(16));
            $partner = PartnerCache::get($this->user['id']);
            $this->assign('partnerInfo',$partner);
            $this->display();
        }
    }

    //席位订金
    public function seatDeposit(){
        PartnerCache::remove($this->user['id']);
        $partner = PartnerCache::get($this->user['id']);
        if($partner['id']){
            $where = array(
                'id' => $partner['city'],
            );
            $city = D('Business/City')->selectCity($where);
            $partner['city'] = $city[0];
        }
        if(IS_POST){
        }else{
            //购物车配置开启的项
            $this->unlockingFooterCart = unlockingFooterCartConfig(array(2,17));
            $this->assign('partnerInfo',$partner);
            $this->display();
        }
    }

    //资格完款
    public function seniority(){
        $partner = PartnerCache::get($this->user['id']);
        if($partner['id']){
            $where = array(
                'id' => $partner['city'],
            );
            $city = D('Business/City')->selectCity($where);
            $partner['city'] = $city[0];
        }
        if(IS_POST){
        }else{
            //购物车配置开启的项
            $this->unlockingFooterCart = unlockingFooterCartConfig(array(2,18));
            $this->assign('partnerInfo',$partner);
            $this->display();
        }
    }
}