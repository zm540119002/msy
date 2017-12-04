<?php
namespace Admin\Controller;
use Think\Controller;

class IndexController extends Controller {
    //后台首页
    public function index(){
        var_dump(getcwd());exit;
        $this->display();
    }
}