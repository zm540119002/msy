<?php
namespace app\factory\controller;

class ManagerManage extends \common\controller\FactoryStoreBase{
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
            return view('public/factory_store_list_tpl');
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
    public function editStoreEmployee(){
        if(!($this->store['id'])){
            return errorMsg('请选择店铺！');
        }
        if(request()->isAjax()){
            $modelManagerManage = new \app\factory\model\ManagerManage();
            $info = $modelManagerManage->editStoreEmployee($this->store['id']);
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
        if(!($this->store['id'])){
            return errorMsg('请选择店铺！');
        }
        if(request()->isAjax()){
            $modelUserStore = new \common\model\UserStore();
            $config = [
                'field' => [
                    'u.id','u.mobile_phone',
                    'us.post','us.duty','us.id user_store_id','us.user_name name',
                ],'leftJoin' => [
                    ['user u','u.id = us.user_id'],
                ],'where' => [
                    ['u.status','=',0],
                    ['us.status','=',0],
                    ['us.type','=',4],
                    ['us.store_id','=',$this->store['id']],
                ],
            ];
            $storeEmployeeList = $modelUserStore->getList($config);
            foreach ($storeEmployeeList as &$user){
                $modelUserStoreNode = new \common\model\UserStoreNode();
                $config = [
                    'field' => [
                        'usn.node_id',
                    ],'where' => [
                        ['usn.status','=',0],
                        ['usn.user_id','=',$user['id']],
                        ['usn.store_id','=',$this->store['id']],
                    ],
                ];
                $userStoreNodeList = $modelUserStoreNode->getList($config);
                $nodeIds = array_unique(array_column($userStoreNodeList,'node_id'));
                if(!empty($nodeIds)){
                    $user['nodeIds'] = $nodeIds;
                }
            }
            $this->assign('list',$storeEmployeeList);
            return view('store_employee_list_tpl');
        }
    }

    /**编辑店铺收货人信息
     */
    public function editStoreConsigneeInfo(){
        if(!($this->store['id'])){
            return errorMsg('请选择店铺！');
        }
        if(request()->isAjax()){
            $modelManagerManage = new \app\factory\model\ManagerManage();
            $res = $modelManagerManage->editStoreConsigneeInfo($this->store['id']);
            return $res;
        }
    }

    /**删除店铺员工
     */
    public function delStoreEmployee(){
        if(!($this->store['id'])){
            return errorMsg('请选择店铺！');
        }
        if(request()->isAjax()){
            $modelManagerManage = new \app\factory\model\ManagerManage();
            return $modelManagerManage->delStoreEmployee($this->store['id'],false);
        }
    }
}