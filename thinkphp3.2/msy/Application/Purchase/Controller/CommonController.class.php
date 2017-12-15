<?php
namespace Purchase\Controller;
use think\Controller;
use web\all\Controller\AuthCompanyAuthoriseController;
class CommonController extends AuthCompanyAuthoriseController {
    //验证机构登记
    public function checkCompanyAuthorise(){
        $returnUrl = session('returnUrl');
        header('Location:'.$returnUrl);
    }
}