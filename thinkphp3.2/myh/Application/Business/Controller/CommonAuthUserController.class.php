<?php
namespace Business\Controller;

use web\all\Controller\AuthUserController;

class CommonAuthUserController extends AuthUserController {
    //验证登记
    public function checkLogin(){
        $this->ajaxReturn(successMsg('成功'));
    }
}