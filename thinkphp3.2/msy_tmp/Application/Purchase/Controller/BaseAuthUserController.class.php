<?php
namespace Purchase\Controller;

use Common\Controller\AuthUserController;

/**
 * 需要验证登录信息的都继承此基类
 */
class BaseAuthUserController extends AuthUserController{
    public function __construct(){
        parent::__construct();
    }
}


