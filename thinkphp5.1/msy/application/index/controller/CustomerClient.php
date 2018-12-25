<?php
namespace app\index\controller;

class CustomerClient extends \common\controller\UserBase{
    //首页
    public function index(){
        if(request()->isAjax()){
        }else{
            return $this->fetch();
        }
    }
}