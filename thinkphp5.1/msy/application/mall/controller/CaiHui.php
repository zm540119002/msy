<?php
namespace app\mall\controller;

class CaiHui extends \think\Controller{
    //商城首页
    public function index(){
        return $this->fetch();
    }

    //商城商品列表页
    public function goods(){
        return $this->fetch();
    }

    //商品详情页
    public function detail(){
        return $this->fetch();
    }
}