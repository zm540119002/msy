<?php
namespace app\factory\controller;

class Account extends FactoryBase
{
    //首页
    public function index(){
        if(request()->isAjax()){
            $modelAccount = new \app\factory\model\Account();
            $info = $modelAccount->edit();
            if($info.status==0){
                return $info;
            }else{
                $this->assign('info',$info);
                return view('info_tpl');
            }
        }else{
            $modelRole = new \app\factory\model\Role();
            $list = $modelRole->getList($this->factory['id']);
            $this->assign('list',$list);
            return $this->fetch();
        }
    }
}