<?php
namespace app\index_admin\controller;

class Order extends Base{
    //首页
    public function manage(){

        return $this->fetch();
    }
    //欢迎页
    public function welcome(){
        return $this->fetch();
    }
}