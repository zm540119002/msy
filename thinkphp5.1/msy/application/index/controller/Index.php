<?php
namespace app\index\controller;

class Index extends \common\controller\Base{
    //首页
    public function index(){
        echo 123;exit;
        return $this->fetch();
    }
}