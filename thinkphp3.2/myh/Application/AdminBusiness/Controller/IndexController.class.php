<?php
namespace AdminBusiness\Controller;
use Think\Controller;
use AdminBusiness\Controller\BaseAuthUserController;
class IndexController extends Controller {
    //后台首页
    public function index(){
        $this->display();
    }
}