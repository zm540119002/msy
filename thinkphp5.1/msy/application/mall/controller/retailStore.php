<?php
namespace app\mall\controller;

class RetailStore extends MallBase{
    /**首页
     */
    public function index(){
        return $this->fetch();
    }

    /**供应商零售店
     */
    public function goods(){
        return $this->fetch();
    }
}