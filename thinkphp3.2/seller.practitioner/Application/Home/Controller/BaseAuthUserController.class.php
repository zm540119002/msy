<?php
namespace Home\Controller;

use Common\Controller\AuthUserController;

/**卖手平台-从业人员端-登录验证基类
 */
class BaseAuthUserController extends AuthUserController{
    protected $seller = null;   //卖手
    protected $sellerAuthorizeUrl = 'SellerPractitioner/SellerAuthorise/index'; //卖手认证URL

    public function __construct(){
        parent::__construct();
    }
}


