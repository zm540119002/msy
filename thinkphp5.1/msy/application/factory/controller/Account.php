<?php
namespace app\factory\controller;

class Account extends \common\controller\UserBase
{
    //首页
    public function index(){
        
        return $this->fetch();
    }
}