<?php
namespace Home\Controller;
use  web\all\Controller\BaseController;
class IndexController extends BaseController {
    //首页
    public function index(){
        if(IS_POST){
        }else{
            $this->display();
        }

    }


}