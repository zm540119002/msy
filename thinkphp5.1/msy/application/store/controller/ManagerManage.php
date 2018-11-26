<?php
namespace app\store\controller;

class ManagerManage extends \common\controller\FactoryStoreBase{
    private $_currentStoreShopList = null;
    public function __construct(){
        parent::__construct();
        //获取当前店铺门店列表
        $this->getStoreShopList($this->store['id']);
        $this->assign('currentStoreShopList',$this->_currentStoreShopList);
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
            $modelManagerManage = new \app\store\model\ManagerManage();
            $info = $modelManagerManage->editStoreEmployee($this->store['id']);
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
        if(!($this->store['id'])){
            return errorMsg('请选择店铺！');
        }
        if(request()->isAjax()){
            $modelManagerManage = new \app\store\model\ManagerManage();
            $info = $modelManagerManage->editShopEmployee($this->store['id']);
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

    //获取店铺门店列表
    private function getStoreShopList($storeId=0){
        $shopList = [];
        if($storeId){
            $modelShop = new \app\store\model\Shop();
            $config = [
                'field' => [
                    's.id','s.name',
                ],'where' => [
                    ['s.status','=',0],
                    ['s.store_id','=',$storeId],
                ],
            ];
            $shopList = $modelShop->getList($config);
        }
        $this->_currentStoreShopList =  $shopList;
    }

    //获取门店员工列表
    public function getShopEmployeeList(){
        if(!($this->store['id'])){
            return errorMsg('请选择店铺！');
        }
        if(request()->isAjax()){
            $modelUserShop = new \app\store\model\UserShop();
            $config = [
                'field' => [
                    'u.id','u.mobile_phone',
                    'us.id user_shop_id','us.post','us.duty','us.shop_id','us.user_name name',
                ],'leftJoin' => [
                    ['common.user u','u.id = us.user_id'],
                ],'where' => [
                    ['u.status','=',0],
                    ['us.status','=',0],
                    ['us.type','=',4],
                    ['us.store_id','=',$this->store['id']],
                ],
            ];
            $shopEmployeeList = $modelUserShop->getList($config);
            foreach ($shopEmployeeList as &$user){
                $modelUserShopNode = new \app\store\model\UserShopNode();
                $config = [
                    'field' => [
                        'usn.node_id',
                    ],'where' => [
                        ['usn.status','=',0],
                        ['usn.user_id','=',$user['id']],
                        ['usn.store_id','=',$this->store['id']],
                    ],
                ];
                $userShopNodeList = $modelUserShopNode->getList($config);
                $nodeIds = array_unique(array_column($userShopNodeList,'node_id'));
                if(!empty($nodeIds)){
                    $user['nodeIds'] = $nodeIds;
                }
            }
            $this->assign('list',$shopEmployeeList);
            return view('shop_employee_list_tpl');
        }
    }

    //获取门店经营地址列表
    public function getShopOperationAddressList(){
        if(!($this->store['id'])){
            return errorMsg('请选择店铺！');
        }
        if(request()->isAjax()){
            $modelShop = new \app\store\model\Shop();
            $config = [
                'field' => [
                    's.id','s.name','s.logo_img','s.operation_mobile_phone','s.operation_fix_phone','s.operation_address',
                ],'where' => [
                    ['s.status','=',0],
                    ['s.store_id','=',$this->store['id']],
                ],
            ];
            $shopList = $modelShop->getList($config);
            $this->assign('list',$shopList);
            return view('shop_operation_address_list_tpl');
        }
    }

    //获取门店收货人地址列表
    public function getShopConsigneeAddressList(){
        if(!($this->store['id'])){
            return errorMsg('请选择店铺！');
        }
        if(request()->isAjax()){
            $modelShop = new \app\store\model\Shop();
            $config = [
                'field' => [
                    's.id','s.name','s.logo_img','s.consignee_mobile_phone','s.consignee_name',
                    's.consignee_address','s.consignee_province','s.consignee_city','s.consignee_area',
                ],'where' => [
                    ['s.status','=',0],
                    ['s.store_id','=',$this->store['id']],
                ],
            ];
            $shopList = $modelShop->getList($config);
            $this->assign('list',$shopList);
            return view('shop_consignee_address_list_tpl');
        }
    }

    /**编辑店铺收货人信息
     */
    public function editStoreConsigneeInfo(){
        if(!($this->store['id'])){
            return errorMsg('请选择店铺！');
        }
        if(request()->isAjax()){
            $modelManagerManage = new \app\store\model\ManagerManage();
            $res = $modelManagerManage->editStoreConsigneeInfo($this->store['id']);
            return $res;
        }
    }

    /**编辑门店经营地址信息
     */
    public function editShopOperationAddress(){
        if(!($this->store['id'])){
            return errorMsg('请选择店铺！');
        }
        if(request()->isAjax()){
            $postData = input('post.');
            //数据验证
            $validateShop = new \app\store\validate\Shop();
            if(!$validateShop->scene('operation_address')->check($postData)){
                return errorMsg($validateShop->getError());
            }
            if(isset($postData['shopId']) && intval($postData['shopId'])){//修改门店经营地址信息
                $modelShop = new \app\store\model\Shop();
                list($postData['operation_province'],$postData['operation_city'],$postData['operation_area']) = $postData['region'];
                $postData['logo_img'] = moveImgFromTemp(config('upload_dir.shop_logo_img'),basename($postData['logo_img']));
                $where = [
                    ['id','=',$postData['shopId']],
                    ['store_id','=',$this->store['id']],
                    ['status','=',0],
                ];
                $res = $modelShop->isUpdate(true)->save($postData,$where);
                if($res===false){
                    return errorMsg('失败',$modelShop->getError());
                }
                return successMsg('成功');
            }
        }else{
            $shopId = input('shopId');
            if(intval($shopId)){
                $modelShop = new \app\store\model\Shop();
                $config = [
                    'field' => [
                        's.id','s.name','s.logo_img','s.operation_mobile_phone','s.operation_fix_phone',
                        's.operation_province','s.operation_city','s.operation_area','s.operation_address',
                    ],'where' => [
                        ['s.status','=',0],
                        ['s.id','=',$shopId],
                        ['s.store_id','=',$this->store['id']],
                    ],
                ];
                $shopInfo = $modelShop->getInfo($config);
                $this->assign('shopInfo',$shopInfo);
            }
            return $this->fetch();
        }
    }

    /**编辑门店收货人地址
     */
    public function editShopConsigneeAddress(){
        if(!($this->store['id'])){
            return errorMsg('请选择店铺！');
        }
        if(request()->isAjax()){
            $postData = input('post.');
            if(!isset($postData['shopId']) || !intval($postData['shopId'])){//修改门店经营地址信息
                return errorMsg('缺少门店ID');
            }
            $postData['consignee_name'] = trim($postData['consignee_name']);
            $postData['consignee_mobile_phone'] = trim($postData['consignee_mobile_phone']);
            $postData['consignee_address'] = trim($postData['consignee_address']);
            //数据验证
            $validateShop = new \app\store\validate\Shop();
            if(!$validateShop->scene('consignee_address')->check($postData)){
                return errorMsg($validateShop->getError());
            }
            list($postData['consignee_province'],$postData['consignee_city'],$postData['consignee_area']) = $postData['region'];
            $postData['logo_img'] = config('upload_dir.shop_logo_img').basename($postData['logo_img']);
            $where = [
                ['id','=',$postData['shopId']],
                ['store_id','=',$this->store['id']],
                ['status','=',0],
            ];
            $modelShop = new \app\store\model\Shop();
            $res = $modelShop->isUpdate(true)->save($postData,$where);
            if($res===false){
                return errorMsg('失败',$modelShop->getError());
            }
            $postData['id'] = $postData['shopId'];
            $this->assign('info',$postData);
            return view('shop_consignee_address_info_tpl');
        }
    }

    /**删除店铺员工
     */
    public function delStoreEmployee(){
        if(!($this->store['id'])){
            return errorMsg('请选择店铺！');
        }
        if(request()->isAjax()){
            $modelManagerManage = new \app\store\model\ManagerManage();
            return $modelManagerManage->delStoreEmployee($this->store['id'],false);
        }
    }

    /**删除门店员工
     */
    public function delShopEmployee(){
        if(!($this->store['id'])){
            return errorMsg('请选择店铺！');
        }
        if(request()->isAjax()){
            $modelManagerManage = new \app\store\model\ManagerManage();
            return $modelManagerManage->delShopEmployee($this->store['id'],false);
        }
    }
}