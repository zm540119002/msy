<?php
namespace app\factory\controller;

class Account extends FactoryBase
{
    //首页
    public function index(){
        if(request()->isAjax()){
            $modelAccount = new \app\factory\model\Account();
            $info = $modelAccount->edit($this->factory['id']);
            if($info['status']==0){
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

    //账号详情
    public function detail(){
        if(request()->isGet()){
            $modelAccount = new \app\factory\model\Account();
            $info = $modelAccount->detail($this->factory['id']);
//            print_r($info);
//            exit;
            $this->assign('info',$info);
            return $this->fetch();
        }
    }

    //获取工厂账号列表
    public function getAccountList(){
        if(request()->isGet()){
            $modelAccount = new \app\factory\model\Account();
            $list = $modelAccount->getList($this->factory['id']);
            $this->assign('list',$list);
            return view('list_tpl');
        }
    }
}