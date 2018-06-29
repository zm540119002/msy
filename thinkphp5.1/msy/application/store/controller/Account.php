<?php
namespace app\store\controller;

class Account extends StoreBase
{
    //首页
    public function index(){
        if(request()->isAjax()){
            $modelAccount = new \app\store\model\Account();
            $info = $modelAccount->edit($this->store['id']);
            if($info['status']==0){
                return $info;
            }else{
                $this->assign('info',$info);
                return view('info_tpl');
            }
        }else{
            $modelRole = new \app\store\model\Role();
            $list = $modelRole->getList($this->store['id']);
            $this->assign('list',$list);
            return $this->fetch();
        }
    }

    //账号详情
    public function detail(){
        if(request()->isGet()){
            $modelAccount = new \app\store\model\Account();
            $info = $modelAccount->detail($this->store['id']);
            $this->assign('info',$info);
            $this->assign('roleList',$info['role']);
            return $this->fetch();
        }
    }

    //设置账户状态
    public function setStatus(){
        if(request()->isPost()){
            $modelUserStore = new \app\store\model\UserStore();
            $info = $modelUserStore->setStatus($this->store['id']);
            return $info;
        }
    }

    //获取工厂账号列表
    public function getAccountList(){
        if(request()->isGet()){
            $modelAccount = new \app\store\model\Account();
            $list = $modelAccount->getList($this->store['id']);
            $this->assign('list',$list);
            return view('list_tpl');
        }
    }

    //用户角色编辑
    public function editRole(){
        $modelAccount = new \app\store\model\Account();
        return $modelAccount->editRole($this->store['id']);
    }
}