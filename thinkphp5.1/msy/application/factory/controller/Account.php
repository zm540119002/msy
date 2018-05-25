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
            $modelRole = new \app\factory\model\Role();
            $list = $modelRole->getList($this->factory['id']);
            $this->assign('list',$list);
            return $this->fetch();
        }
    }
}