<?php
namespace app\store\controller;

class ManagerManage extends \common\controller\UserBase{
    private $_storeList = null;
    private $_store = null;
    private $_managerFactoryList = null;
    private $_defaultDialog = null;

    public function __construct(){
        parent::__construct();
        //获取店铺列表
        $this->_getStoreList();
        //获取当前店铺
        $this->_getStoreInfo(input('storeId'));
        //获取店家店铺列表
        $this->_getManagerFactoryList();
        $this->assign('store', $this->_store);
        $this->assign('managerFactoryList', $this->_managerFactoryList);
        $this->assign('defaultDialog', $this->_defaultDialog);
    }

    /**首页
     */
    public function index(){
        if(request()->isAjax()){
            $modelManagerManage = new \app\store\model\ManagerManage();
            $this->_managerFactoryList = $modelManagerManage->getList($this->user['id']);
            $this->assign('list',$this->_managerFactoryList);
            return view('list_tpl');
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

    /**编辑管理员
     */
    public function edit(){
        if(request()->isAjax()){
            return 123;
            $modelManagerManage = new \app\store\model\ManagerManage();
            $info = $modelManagerManage->edit($this->user['id'],$this->user['type']);
            if($info['status']==0){
                return $info;
            }else{
                $this->assign('info',$info);
                return view('info_tpl');
            }
        }
    }

    /**删除管理员
     */
    public function del(){
        if(request()->isAjax()){
            return 123;
            $modelManagerManage = new \app\store\model\ManagerManage();
            return $modelManagerManage->del($this->user['id']);
        }
    }

    /**获取店铺列表
     */
    private function _getStoreList(){
        $model = new \common\model\UserStore();
        $config = [
            'where' => [
                ['us.status','=',0],
                ['us.user_id','=',$this->user['id']],
            ],'join' => [
                ['store s','s.id = us.store_id','left'],
                ['factory f','f.id = us.factory_id','left'],
                ['record r','r.id = s.foreign_id','left'],
                ['brand b','b.id = s.foreign_id','left'],
            ],'field' => [
                's.id','s.store_type','s.run_type','s.is_default','s.operational_model',
                'case s.store_type when 1 then r.logo_img when 2 then b.brand_img END as logo_img',
                'case s.store_type when 1 then r.short_name when 2 then b.name END as store_name',
                'f.id factory_id','f.name','f.type',
            ],
        ];
        $storeList = $model->getList($config);
        $this->_storeList = $storeList;

    }

    /**获取当前店铺
     */
    private function _getStoreInfo($storeId=0){
        if($storeId){
            $model = new \common\model\Store();
            $config = [
                'where' => [
                    ['s.status','=',0],
                    ['s.id','=',$storeId],
                ],'join' => [
                    ['factory f','f.id = s.factory_id','left'],
                    ['record r','r.id = s.foreign_id','left'],
                    ['brand b','b.id = s.foreign_id','left'],
                ],'field' => [
                    's.id','s.store_type','s.run_type','s.is_default','s.operational_model',
                    'case s.store_type when 1 then r.logo_img when 2 then b.brand_img END as logo_img',
                    'case s.store_type when 1 then r.short_name when 2 then b.name END as store_name',
                    'f.id factory_id','f.name','f.type',
                ],
            ];
            $storeInfo = $model->getInfo($config);
            $this->_store = $storeInfo;
        }elseif(count($this->_storeList)==1){
            $this->_store = $this->_storeList[0];
        }elseif(count($this->_storeList)>1){
            $this->_defaultDialog = true;
        }
    }

    /**获取店家店铺列表
     */
    private function _getManagerFactoryList(){
        $storeListCount = count($this->_storeList);
        if($storeListCount>1){
            foreach ($this->_storeList as $item) {
                $storeInfoArr = [
                    'id' => $item['id'],
                    'store_name' => $item['store_name'],
                    'logo_img' => $item['logo_img'],
                    'store_type' => $item['store_type'],
                    'run_type' => $item['run_type'],
                    'operational_model' => $item['operational_model'],
                    'is_default' => $item['is_default'],
                ];
                $factory_id_arr = array_column($this->_managerFactoryList,'factory_id');
                if(!in_array($item['factory_id'],$factory_id_arr)){//factory不存在
                    $this->_managerFactoryList[] = [
                        'factory_id' => $item['factory_id'],
                        'name' => $item['name'],
                        'type' => $item['type'],
                        'store_list' => [$storeInfoArr],
                    ];
                }else{//factory存在
                    foreach ($this->_managerFactoryList as &$key){
                        if($key['factory_id'] == $item['factory_id']){
                            $key['store_list'][] = $storeInfoArr;
                        }
                    }
                }
            }
        }
    }
}