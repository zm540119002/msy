<?php
namespace app\index\controller;

class CustomerTest extends \common\controller\UserBase{
    //首页
    public function index(){
        return $this->fetch();
    }
}