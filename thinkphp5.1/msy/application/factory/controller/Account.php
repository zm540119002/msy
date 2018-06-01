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

    public function getAccountList(){
        if(request()->isGet()){
            $modelUserFactory = new \app\factory\model\UserFactory();
            $where = [
                'u.factory_id' => $this->factory['id'],
            ];
            $field = [
                'uu.id','uu.name','uu.nickname','uu.mobile_phone','uu.status','u.is_default',
            ];
            $join = [
                ['common.user uu','uu.id = u.user_id','LEFT'],
            ];
            $list = $modelUserFactory->getList($where,$field,$join);
            $this->assign('list',$list);
            return view('list_tpl');
        }
    }
}