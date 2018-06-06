<?php
namespace app\factory\controller;

class Operation extends StoreBase
{
    //运营管理首页
    public function Index(){
        return $this->fetch();
    }

}