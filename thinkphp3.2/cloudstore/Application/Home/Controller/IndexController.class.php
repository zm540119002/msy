<?php
namespace Home\Controller;

use web\all\Controller\BaseController;

class IndexController extends BaseController {
    //云店首页
    public function index(){
        $this->display();
    }

    //云店设置
    public function storeSettings(){
        $this->display();
    }
}