<?php
namespace Business\Controller;

use web\all\Controller\AuthPartnerController;

class PartnerController extends AuthPartnerController {
    //首页
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
            $this->partnerInfo = $this->partner;
            //省市数组
            $this->assign('provinceList',getProvinceCity());
            $this->display();
        }
    }

    //授权我的代理商
    public function authoriseAgent(){
        if(IS_POST){
            if(isset($_POST['mobile_phone']) && isMobile($_POST['mobile_phone'])){
                $mobilePhone = I('post.mobile_phone','','string');
                if(checkAgentByMobilePhone($mobilePhone)){
                    $this->ajaxReturn(errorMsg('已经是代理商，请检查手机号码！'));
                }
                $modelAgent = D('Agent');
                $_POST['partner_id'] = $this->partner['id'];
                $_POST['type'] = 1;
                $_POST['auth_status'] = 1;
                $_POST['create_time'] = time();
                $res = $modelAgent->addAgent();
                if($res['status'] == 0){
                    $this->ajaxReturn(errorMsg($modelAgent->getLastSql()));
                }
                $this->ajaxReturn(successMsg('授权成功'));
            }
        }else{
            //购物车配置开启的项
            $this->unlockingFooterCart = unlockingFooterCartConfig(array(22));
            //合伙人信息
            $this->partnerInfo = $this->partner;
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