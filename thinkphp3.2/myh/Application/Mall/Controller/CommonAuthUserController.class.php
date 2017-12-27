<?php
namespace Purchase\Controller;

use web\all\Controller\AuthUserController;

class CommonController extends AuthUserController {
    //验证登记
    public function checkLogin(){
        $this->ajaxReturn(successMsg('成功'));
    }
}