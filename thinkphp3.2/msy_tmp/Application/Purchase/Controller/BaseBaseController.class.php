<?php
namespace Purchase\Controller;

/**
 * 不需验证登录及机构信息的类都继承此类
 */
class BaseBaseController extends \Common\Controller\BaseController{
    public function __construct(){
        parent::__construct();
    }
}


