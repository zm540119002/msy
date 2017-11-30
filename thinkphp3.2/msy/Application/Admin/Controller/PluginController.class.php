<?php
namespace Admin\Controller;
use Think\Controller;
use web\all\Controller\BaseController;

class PluginController extends BaseController {
    //后台首页
    public function index(){
        $this->display();
    }
}