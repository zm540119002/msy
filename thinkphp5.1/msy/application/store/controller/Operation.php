<?php
namespace app\factory\controller;

class Operation extends StoreBase
{
    //运营管理首页
    public function Index(){
        return $this->fetch();
    }
    //店铺管理设置页
    public function set(){
        return $this->fetch();
    }

}