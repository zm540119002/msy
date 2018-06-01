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
//            $modelUserFactory = new \app\factory\model\UserFactory();
//            $where = [
//                'u.factory_id' => $this->factory['id'],
//            ];
//            $field = [
//                'u.user_id id','u.is_default','u.type','uu.name','uu.nickname','uu.mobile_phone',
//            ];
//            $join = [
//                ['common.user uu','uu.id = u.user_id','LEFT'],
//            ];
//            $list = $modelUserFactory->getList($where,$field,$join);
//            $this->assign('list',$list);
//            return view('list_tpl');

            $modelFactory = new \app\factory\model\Factory();
            $field = [
                'id',
            ];
            $factory = \app\factory\model\Factory::get($this->factory['id']);
            $users = $factory->users;
            $list = [];
            foreach ($users as $user){
                if($user->pivot->type==2){
                    $arr = [
                        'id' => $user->id,
                        'name' => $user->name,
                        'nickname' => $user->nickname,
                        'mobile_phone' => $user->mobile_phone,
                        'status' => $user->status,
                    ];
                    $list[] = $arr;
                }
            }
            $this->assign('list',$list);
            return view('list_tpl');
            return $list;
        }
    }
}