<?php
namespace app\store\controller;

class Index extends \common\controller\UserBase{
    /**首页
     */
    public function index(){
        return $this->fetch();
    }
}