<?php
namespace app\api\controller;

class AfterSale extends HssBase{
    /**首页
     */
    public function index(){
        $unlockingFooterCart = unlockingFooterCartConfig([16]);
        $this->assign('unlockingFooterCart', $unlockingFooterCart);
        return $this->fetch();
    }

    public function afterSale(){
        
    }
}