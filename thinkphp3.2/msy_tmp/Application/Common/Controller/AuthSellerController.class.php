<?php
namespace Common\Controller;

use Common\Cache\SellerCache;

/**卖手认证控制器基类
 */
class AuthSellerController extends AuthUserController{
    protected $seller = null;
    protected $sellerAuthoriseUrl = 'UserCenter/SellerAuthorise/index';

    public function __construct(){
        parent::__construct();

        SellerCache::remove($this->user['id']);
        $this->seller = SellerCache::get($this->user['id']);
        if(!$this->seller){
            $this->error(C('ERROR_SELLER_AUTHORISE_REMIND'),U($this->sellerAuthoriseUrl));
        }
    }
}


