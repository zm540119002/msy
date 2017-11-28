<?php
namespace Home\Controller;

use web\all\Controller\AuthCompanyRegisterController;

/**
 * 需要验证登录信息的都继承此基类
 */
class BaseAuthCompanyRegisterController extends AuthCompanyRegisterController{
    public function __construct(){
        parent::__construct();
    }
}


