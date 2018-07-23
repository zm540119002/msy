<?php
namespace app\mall\controller;

class Index extends MallBase{
    /**首页
     */
    public function index(){
        return $this->fetch();
    }
}