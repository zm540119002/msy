<?php
namespace Home\Controller;

use web\all\Cache\CompanyCache;
use web\all\Controller\AuthCompanyRegisterController;

class CompanyAuthoriseController extends AuthCompanyRegisterController {
    //机构认证-首页
    public function index(){
        if(IS_POST){
        }else{
            if($this->company['auth_status'] == 1){//已登记
                $this->display();
            }else if($this->company['auth_status'] == 2){//已认证
                $this->display('authorizeComplete');
            }
        }
    }

    //机构认证状态查询
    public function authStatusSearch(){
        $modelCompany = D('Company');
        $where = array(
            'id'=>$this->company['id']
        );
        $res = $modelCompany->where($where)->field('auth_status')->find();
        if(!$res){
            $this->ajaxReturn(errorMsg($modelCompany->getError()));
        }
        $this->ajaxReturn(successMsg($res));
    }

    //机构认证-基本资料
    public function authorizeInfo(){
        $modelCompany = D('Company');
        if(IS_POST){
            $rules = array(
                array('name','require','美容机构完整名称必须！'),
                array('shorten_name','require','美容机构简称必须！'),
                array('registrant','require','申请人姓名必须！'),
                array('registrant_mobile','isMobile','请输入正确的手机号码',0,'function'),
            );
            $_POST['companyId'] = $this->company['id'];
            $where = array(
                'user_id' => $this->user['id'],
            );
            $res = $modelCompany->saveCompany($where,$rules);
            CompanyCache::remove($this->user['id']);
            $this->ajaxReturn($res);
        }else{
            $this->assign('company',$this->company);
            $this->display();
        }
    }

    //机构认证-认证资料
    public function authorizeData(){
        if(IS_POST){
            if(count($_POST)){
                $modelCompany = D('Company');
                $where = array(
                    'user_id' => $this->user['id'],
                );
                $companyInfo = $modelCompany->selectCompany($where);
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
                    $res = $modelCompany->saveCompany($where);
                    if(!$res['status']){
                        $this->ajaxReturn(errorMsg($res['info']));
                    }
                    CompanyCache::remove($this->user['id']);
                }
                $this->ajaxReturn(successMsg('成功'));
            }
        }else{
            $this->assign('company',$this->company);
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
        $modelCompany = D('Company');
        $where =array(
            'user_id' => $this->user['id'],
        );
        $_POST['auth_status'] = 2;
        $res = $modelCompany->saveCompany($where);
        if(!$res['status']){
            $this->ajaxReturn(errorMsg($res['info']));
        }
        CompanyCache::remove($this->user['id']);
        $this->ajaxReturn(successMsg($res['info']));
    }
}