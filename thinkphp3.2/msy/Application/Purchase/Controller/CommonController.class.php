<?php
namespace Purchase\Controller;
use web\all\Controller\AuthUserController;
class CommonController extends AuthUserController {
    //验证登录
    public function checkLogin(){
        if(!IS_POST){
            $this->ajaxReturn(errorMsg(C('NOT_POST')));
        }
        $this->ajaxReturn(successMsg('已登录'));
    }
}