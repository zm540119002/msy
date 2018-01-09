<?php
namespace Home\Controller;

use web\all\Cache\PartnerCache;
use web\all\Controller\AuthUserController;

class PartnerAuthoriseController extends AuthUserController {
    //合伙人认证-首页
    public function index(){
        if(IS_POST){
        }else{
            if($this->company['auth_status'] == 2){//已认证
                $this->display('authorizeComplete');
            }else{//未认证
                //购物车配置开启的项
                $this->unlockingFooterCart = unlockingFooterCartConfig(array(14));
                $this->display();
            }
        }
    }

    //登记资料
    public function register(){
        $modelPartner = D('Partner');
        if(IS_POST){
            $rules = array(
                array('name','require','美容机构完整名称必须！'),
                array('shorten_name','require','美容机构简称必须！'),
                array('registrant','require','申请人姓名必须！'),
                array('registrant_mobile','isMobile','请输入正确的手机号码',0,'function'),
            );
            if(isset($_POST['companyId']) && intval($_POST['companyId'])){//修改
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
            $company = PartnerCache::get($this->user['id']);
            $this->assign('company',$company);
            $this->display();
        }
    }

    //机构认证-认证资料
    public function authorizeData(){
        if(IS_POST){
            if(count($_POST)){
                $modelPartner = D('Partner');
                $where = array(
                    'user_id' => $this->user['id'],
                );
                $companyInfo = $modelPartner->selectPartner($where);
                $companyInfo = $companyInfo[0];

                $sign = false;//更新数据库标志
                foreach ($_POST as $key => &$val) {
                    if($val){
                        if(!$companyInfo[$key]){//新增
                            $sign = true;
                            $val = $this->moveImgFromTemp(C('COMPANY_AUTHORISE_PATH'),basename($val));
                        }else if($companyInfo[$key] && basename($val) != basename($companyInfo[$key]) ){//修改
                            $sign = true;
                            $this->delImg($companyInfo[$key]);
                            $val = $this->moveImgFromTemp(C('COMPANY_AUTHORISE_PATH'),basename($val));
                        }
                    }
                }
                if($sign){//有修改
                    $res = $modelPartner->savePartner($where);
                    if(!$res['status']){
                        $this->ajaxReturn(errorMsg($res['info']));
                    }
                    PartnerCache::remove($this->user['id']);
                }
                $this->ajaxReturn(successMsg('成功'));
            }
        }else{
            $company = PartnerCache::get($this->user['id']);
            $this->assign('company',$company);
            $this->display();
        }
    }

    //机构认证-提交申请
    public function authorizeSubmit(){
        if(IS_POST){
            //暂时在这里认证成功，以后在后台确认
            $this->companyAuthSuccess();
        }else{
            $this->display();
        }
    }

    //机构认证-认证完成
    public function authorizeComplete(){
        if(IS_POST){
        }else{
            $this->display();
        }
    }

    //机构认证成功
    public function companyAuthSuccess(){
        $modelPartner = D('Partner');
        $where =array(
            'user_id' => $this->user['id'],
        );
        $_POST['auth_status'] = 2;
        $res = $modelPartner->savePartner($where);
        if($res['status']==0){
            $this->ajaxReturn(errorMsg($res));
        }
        PartnerCache::remove($this->user['id']);
        $res = array();
        $res['returnUrl'] = session('returnUrl');
        $this->ajaxReturn(successMsg($res));
    }
}