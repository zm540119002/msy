<?php
namespace app\store\controller;

class ManagerManage extends ManagerManageBase{
    public function __construct(){
        parent::__construct();
    }

    /**首页
     */
    public function index(){
        if(request()->isAjax()){
        }else{
            return $this->fetch();
        }
    }

    /**店铺管理
     */
    public function manage(){
        if(request()->isAjax()){
        }else{
            //岗位
            $post = config('permission.post');
            $this->assign('post', $post);
            //职务
            $duty = config('permission.duty');
            $this->assign('duty', $duty);
            //鉴权
            $authentication = config('permission.authentication');
            $this->assign('authentication', $authentication);

            return $this->fetch();
        }
    }

    /**店铺员工-编辑
     */
    public function storeEmployeeEdit(){
        $currentStore = \common\cache\Store::getCurrentStoreInfo();
        if(!($currentStore['id'])){
            return errorMsg('请选择店铺！');
        }
        if(request()->isAjax()){
            $modelManagerManage = new \app\store\model\ManagerManage();
            $info = $modelManagerManage->storeEmployeeEdit($currentStore['id']);
            if($info['status']==0){
                return $info;
            }else{
                $this->assign('info',$info);
                return view('store_employee_info_tpl');
            }
        }
    }

    //获取店铺员工列表
    public function getStoreEmployeeList(){
        $currentStore = \common\cache\Store::getCurrentStoreInfo();
        if(!($currentStore['id'])){
            return errorMsg('请选择店铺！');
        }
        if(request()->isAjax()){
            $modelUserStore = new \common\model\UserStore();
            $config = [
                'field' => [
                    'u.nickname','u.mobile_phone',
                    'us.post','us.duty',
                ],'leftJoin' => [
                    ['user u','u.id = us.user_id'],
                ],'where' => [
                    ['u.status','=',0],
                    ['us.status','=',0],
                    ['us.type','=',4],
                    ['us.store_id','=',$currentStore['id']],
                    ['us.user_id','=',$this->user['id']],
                ],
            ];
            $list = $modelUserStore->getList($config);
            $this->assign('list',$list);
            return view('store_employee_list_tpl');
        }else{
            return $this->fetch();
        }
    }


    /**删除管理员
     */
    public function del(){
        $currentStore = \common\cache\Store::getCurrentStoreInfo();
        if(!($currentStore['id'])){
            return errorMsg('请选择店铺！');
        }
        if(request()->isAjax()){
            $modelManagerManage = new \app\store\model\ManagerManage();
            return $modelManagerManage->del($this->user['id']);
        }
    }
}