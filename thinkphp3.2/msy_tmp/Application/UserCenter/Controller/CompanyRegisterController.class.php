<?php
namespace UserCenter\Controller;

use Common\Cache\CompanyCache;

class CompanyRegisterController extends BaseAuthUserController {
    //机构登记-基本资料
    public function registerInfo(){
        if(IS_POST){
            $rules = array(
                array('name','require','美容机构全称必须！'),
                array('shorten_name','require','美容机构简称必须！'),
                array('registrant','require','您的姓名必须！'),
                array('registrant_mobile','require','您的手机号必须！'),
                array('registrant_mobile','isMobile','请输入正确的手机号码',0,'function'),
            );
            if( isset($_POST['companyId']) && intval($_POST['companyId']) ){
                $this->saveCompany($rules);
            }else{
                $this->addCompany($rules);
            }
        }else{
            $company = CompanyCache::get($this->user['id']);
            $this->assign('company',$company);

            $this->display();
        }
    }

    //机构登记-收货人地址
    public function registerAddress(){
        if(IS_POST){
            $rules = array(
                array('consignee','require','收货人姓名必须！'),
                array('consignee_mobile','require','收货人手机号码必须！'),
                array('consignee_mobile','isMobile','请输入正确的手机号码',0,'function'),
            );
            if( isset($_POST['companyId']) && intval($_POST['companyId']) ){
                //已登记
                $_POST['auth_status'] = 1;
                $this->saveCompany($rules);
            }else{
                $this->addCompany($rules);
            }
        }else{
            $company = CompanyCache::get($this->user['id']);
            $this->assign('company',$company);

            $this->display();
        }
    }

    //机构登记-登记完成
    public function registerComplete(){
        if(IS_POST){
            $backUrl = session('backUrl');
            $this->success($backUrl ? $backUrl : U('Index/index'));
        }else{
            $this->display();
        }
    }
}