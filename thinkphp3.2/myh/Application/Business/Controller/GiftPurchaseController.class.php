<?php
namespace Business\Controller;

use web\all\Controller\AuthUserController;

class GiftPurchaseController extends AuthUserController {
    //礼品采购-首页
    public function index(){
        $this->display();
    }
}