<?php
namespace app\store\controller;

class Manager extends StoreBase
{
    /**首页
     */
    public function index(){
        return $this->fetch();
    }

    /**新增管理员
     */
    public function add(){
        return $this->fetch();
    }
}