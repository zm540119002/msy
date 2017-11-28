<?php
namespace Purchase\Controller;
use web\all\Controller\AuthUserController;
use  web\all\Controller\BaseController;
class JointPurchaseController extends BaseController {
    //联合采购
    public function index(){
        if(IS_POST){
        }else{
            $this->display();
        }
    }
}