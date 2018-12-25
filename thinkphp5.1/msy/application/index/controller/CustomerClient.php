<?php
namespace app\index\controller;

class CustomerClient extends \common\controller\UserBase{
    //首页
    public function index(){
        if(request()->isAjax()){
            $this->success('成功');
        }else{
            return $this->fetch();
        }
    }
}