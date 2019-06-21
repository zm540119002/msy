<?php
namespace app\index_admin\controller;

class Index extends Base{
    //首页
    public function index(){
        if(request()->isAjax()){
        }else{
            return $this->fetch();
        }
    }
    //欢迎页
    public function welcome(){
        if(request()->isAjax()){
        }else{
            return $this->fetch();
        }
    }
}