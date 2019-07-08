<?php
namespace app\index\controller;

class Company extends \common\controller\UserBase {
    /**首页
     */
    public function index(){

        return $this->fetch();
    }
}