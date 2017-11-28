<?php
namespace Home\Controller;

use web\all\Cache\CompanyCache;
use web\all\Controller\AuthUserController;

class CompanyRegisterController extends AuthUserController {
    //机构登记-基本资料
    public function registerInfo(){
        $modelCompany = D('Company');
        if(IS_POST){
            $rules = array(
                array('name','require','美容机构全称必须！'),
                array('shorten_name','require','美容机构简称必须！'),
                array('registrant','require','您的姓名必须！'),
                array('registrant_mobile','require','您的手机号必须！'),
                array('registrant_mobile','isMobile','请输入正确的手机号码',0,'function'),
            );
            if( isset($_POST['companyId']) && intval($_POST['companyId']) ){
                $where = array(
                    'user_id' => $this->user['id'],
                );
                $res = $modelCompany->saveCompany($where,$rules);
                CompanyCache::remove($this->user['id']);
            }else{
                $_POST['user_id'] = $this->user['id'];
                $_POST['create_time'] = time();
                $res = $modelCompany->addCompany($rules);
            }
            $this->ajaxReturn($res);
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