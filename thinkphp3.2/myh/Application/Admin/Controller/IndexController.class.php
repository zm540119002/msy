<?php
namespace Admin\Controller;
use Think\Controller;
use Admin\Controller\BaseAuthUserController;
class IndexController extends BaseAuthUserController {
    //后台首页
    public function index(){
        $this->display();
    }

}