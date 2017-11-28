<?php
namespace SellerCompany\Controller;

use Common\Controller\AuthCloudstoreShopController;

/**
 * 需要验证云店-门店的都需要继承此基类
 */
class BaseAuthCloudstoreShopController extends AuthCloudstoreShopController{
    public function __construct(){
        parent::__construct();
    }
}


