<?php
namespace Mall\Controller;

use  web\all\Controller\AuthUserController;

class GroupBuyController extends AuthUserController{
    public function index(){
        $this->display();
    }

    //发起微团购
    public function send(){
        if(IS_POST){
        }else{
        }
    }
}
