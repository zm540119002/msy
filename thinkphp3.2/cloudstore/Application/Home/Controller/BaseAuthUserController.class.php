<?php
namespace Home\Controller;

use Common\Controller\AuthUserController;

/**云店登录验证基类
 */
class BaseAuthUserController extends AuthUserController{
    public function __construct(){
        parent::__construct();
    }
}


