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
                    'u.id','u.nickname name','u.mobile_phone',
                    'us.post','us.duty','us.id user_store_id',
                ],'leftJoin' => [
                    ['user u','u.id = us.user_id'],
                ],'where' => [
                    ['u.status','=',0],
                    ['us.status','=',0],
                    ['us.type','=',4],
                    ['us.store_id','=',$currentStore['id']],
                ],
            ];
            $list = $modelUserStore->getList($config);
            foreach ($list as &$user){
                $modelUserStoreNode = new \common\model\UserStoreNode();
                $config = [
                    'field' => [
                        'usn.node_id',
                    ],'where' => [
                        ['usn.status','=',0],
                        ['usn.user_id','=',$user['id']],
                        ['usn.store_id','=',$currentStore['id']],
                    ],
                ];
                $userStoreNodeList = $modelUserStoreNode->getlist($config);
                $nodeIds = array_unique(array_column($userStoreNodeList,'node_id'));
                if(!empty($nodeIds)){
                    $user['nodeIds'] = $nodeIds;
                }
            }
            $this->assign('list',$list);
            return view('store_employee_list_tpl');
        }
    }

    //获取门店员工列表
    public function getShopEmployeeList(){
        $currentStore = \common\cache\Store::getCurrentStoreInfo();
        if(!($currentStore['id'])){
            return errorMsg('请选择店铺！');
        }
        if(request()->isAjax()){
            $modelUserStore = new \common\model\UserStore();
            $config = [
                'field' => [
                    'u.id','u.nickname name','u.mobile_phone',
                    'us.post','us.duty','us.id user_store_id',
                ],'leftJoin' => [
                    ['user u','u.id = us.user_id'],
                ],'where' => [
                    ['u.status','=',0],
                    ['us.status','=',0],
                    ['us.type','=',4],
                    ['us.store_id','=',$currentStore['id']],
                ],
            ];
            $list = $modelUserStore->getList($config);
            foreach ($list as &$user){
                $modelUserStoreNode = new \common\model\UserStoreNode();
                $config = [
                    'field' => [
                        'usn.node_id',
                    ],'where' => [
                        ['usn.status','=',0],
                        ['usn.user_id','=',$user['id']],
                        ['usn.store_id','=',$currentStore['id']],
                    ],
                ];
                $userStoreNodeList = $modelUserStoreNode->getlist($config);
                $nodeIds = array_unique(array_column($userStoreNodeList,'node_id'));
                if(!empty($nodeIds)){
                    $user['nodeIds'] = $nodeIds;
                }
            }
            $this->assign('list',$list);
            return view('store_employee_list_tpl');
        }
    }

    /**编辑店铺收货人信息
     */
    public function editStoreConsigneeInfo(){
        $currentStore = \common\cache\Store::getCurrentStoreInfo();
        if(!($currentStore['id'])){
            return errorMsg('请选择店铺！');
        }
        if(request()->isAjax()){
            $modelManagerManage = new \app\store\model\ManagerManage();
            $res = $modelManagerManage->editStoreConsigneeInfo($currentStore['id']);
            return $res;
        }
    }

    /**删除店铺员工
     */
    public function delStoreEmployee(){
        $currentStore = \common\cache\Store::getCurrentStoreInfo();
        if(!($currentStore['id'])){
            return errorMsg('请选择店铺！');
        }
        if(request()->isAjax()){
            $modelManagerManage = new \app\store\model\ManagerManage();
            return $modelManagerManage->delStoreEmployee($currentStore['id'],false);
        }
    }
}