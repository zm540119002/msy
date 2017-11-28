<?php
namespace Cloudstore\Controller;

use Common\Controller\BaseController;

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