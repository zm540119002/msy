<?php
namespace app\cloud_twitter\controller;

class Index extends \common\controller\UserBase{
    /**首页
     */
    public function index(){
        return $this->fetch();
    }
}