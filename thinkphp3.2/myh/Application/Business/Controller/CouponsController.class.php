<?php
namespace Business\Controller;

use web\all\Controller\AuthUserController;

class CouponsController extends AuthUserController {
    //代金券-列表页
    public function couponsList(){
        $modelCoupons = D('Coupons');
        if(IS_POST){
        }else{
            $this->display();
        }
    }

    //代金券-立即领取
    public function getNow(){
        $modelCoupons = D('Coupons');
        if(IS_POST){
        }else{
            $this->display();
        }
    }
}