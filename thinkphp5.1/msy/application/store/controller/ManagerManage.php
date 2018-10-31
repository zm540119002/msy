<?php
namespace app\store\controller;

class ManagerManage extends FactoryStoreBase{
    protected $currentStore = null;
    protected $currentShop = null;

    public function __construct(){
        parent::__construct();

        $this->currentStore = \common\cache\Store::getCurrentStoreInfo();
        if(isset($this->currentStore['id']) && $this->currentStore['id']){
            $modelUserShop = new \app\store\model\UserShop();
            $config = [
                'field' => [
                    's.id','s.name',
                    'u.nickname','u.mobile_phone',
                    'us.id user_shop_id',
                ],'leftJoin' => [
                    ['shop s','s.id = us.shop_id'],
                    ['common.user u','u.id = us.user_id'],
                ],'where' => [
                    ['s.status','=',0],
                    ['us.status','=',0],
                    ['us.store_id','=',$this->currentStore['id']],
                ],
            ];
            $shopList = $modelUserShop->getList($config);
            $this->assign('shopList',$shopList);
        }
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
    public function editStoreEmployee(){
        if(!($this->currentStore['id'])){
            return errorMsg('请选择店铺！');
        }
        if(request()->isAjax()){
            $modelManagerManage = new \app\store\model\ManagerManage();
            $info = $modelManagerManage->editStoreEmployee($this->currentStore['id']);
            if($info['status']==0){
                return $info;
            }else{
                $this->assign('info',$info);
                return view('store_employee_info_tpl');
            }
        }
    }

    /**门店员工-编辑
     */
    public function editShopEmployee(){
        if(!($this->currentStore['id'])){
            return errorMsg('请选择店铺！');
        }
        if(request()->isAjax()){
            $modelManagerManage = new \app\store\model\ManagerManage();
            $info = $modelManagerManage->editShopEmployee($this->currentStore['id']);
            if($info['status']==0){
                return $info;
            }else{
                $this->assign('info',$info);
                return view('shop_employee_info_tpl');
            }
        }
    }

    //获取店铺员工列表
    public function getStoreEmployeeList(){
        if(!($this->currentStore['id'])){
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
                    ['us.store_id','=',$this->currentStore['id']],
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
                        ['usn.store_id','=',$this->currentStore['id']],
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
        if(!($this->currentStore['id'])){
            return errorMsg('请选择店铺！');
        }
        if(request()->isAjax()){
            $modelUserShop = new \app\store\model\UserShop();
            $config = [
                'field' => [
                    'u.id','u.nickname','u.mobile_phone',
                    'us.post','us.duty',
                ],'leftJoin' => [
                    ['common.user u','u.id = us.user_id'],
                ],'where' => [
                    ['u.status','=',0],
                    ['us.status','=',0],
                    ['us.type','=',4],
                    ['us.store_id','=',$this->currentStore['id']],
                ],
            ];
            $shopEmployeeList = $modelUserShop->getList($config);
            $this->assign('list',$shopEmployeeList);
            return view('shop_employee_list_tpl');
        }
    }

    /**编辑店铺收货人信息
     */
    public function editStoreConsigneeInfo(){
        if(!($this->currentStore['id'])){
            return errorMsg('请选择店铺！');
        }
        if(request()->isAjax()){
            $modelManagerManage = new \app\store\model\ManagerManage();
            $res = $modelManagerManage->editStoreConsigneeInfo($this->currentStore['id']);
            return $res;
        }
    }

    /**删除店铺员工
     */
    public function delStoreEmployee(){
        if(!($this->currentStore['id'])){
            return errorMsg('请选择店铺！');
        }
        if(request()->isAjax()){
            $modelManagerManage = new \app\store\model\ManagerManage();
            return $modelManagerManage->delStoreEmployee($this->currentStore['id'],false);
        }
    }
}