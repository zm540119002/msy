<?php
namespace app\factory\controller;

class Account extends FactoryBase
{
    //首页
    public function index(){
        $modelUser = new \common\model\User();
        if(request()->isAjax()){
            $info = $modelUser->edit();
            $this->assign('info',$info);
            return view('info_tpl');
        }else{
            return $this->fetch();
        }
    }
}