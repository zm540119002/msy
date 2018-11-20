<?php
namespace common\controller;

class FactoryStoreBase extends UserBase{
    protected $_currentStore = null;
    protected $_storeList = null;
    protected $_factoryStoreList = null;

    public function __construct(){
        parent::__construct();
        //采购商店铺列表
        $this->getFactoryStoreList();
        //获取当前店铺
        $storeId = (int)input('storeId');
        $this->getCurrentStoreInfo($storeId);
    }

    /**获取当前店铺信息
     */
    protected function getCurrentStoreInfo($storeId=0){
        $countStoreList = count($this->_storeList);
        if($storeId){
            $model = new \common\model\Store();
            $config = [
                'field' => [
                    's.id','s.store_type','s.run_type','s.is_default','s.operational_model',
                    's.consignee_name','s.consignee_mobile_phone','s.province','s.city','s.area','s.detail_address',
                    'case s.store_type when 1 then r.logo_img when 2 then b.brand_img END as logo_img',
                    'case s.store_type when 1 then r.short_name when 2 then b.name END as store_name',
                    'f.id factory_id','f.name factory_name','f.type factory_type',
                ],'join' => [
                    ['factory f','f.id = s.factory_id','left'],
                    ['record r','r.id = s.foreign_id','left'],
                    ['brand b','b.id = s.foreign_id','left'],
                ],'where' => [
                    ['s.status','=',0],
                    ['s.id','=',$storeId],
                ],
            ];
            $storeInfo = $model->getInfo($config);
            $this->_currentStore = $storeInfo;
        }elseif($countStoreList==1){
            $this->_currentStore = $this->_storeList[0];
            $this->_currentStore['id'] = $this->_currentStore['store_id'];
        }
        \common\cache\Store::cacheCurrentStoreInfo($this->_currentStore);
        $this->assign('currentStore', $this->_currentStore);
    }

    /**组装店铺列表
     */
    protected function getFactoryStoreList(){
        $this->getStoreList();
        $storeListCount = count($this->_storeList);
        if($storeListCount>0){
            foreach ($this->_storeList as $item) {
                $storeInfoArr = [
                    'store_id' => $item['store_id'],
                    'store_name' => $item['store_name'],
                    'store_type' => $item['store_type'],
                    'run_type' => $item['run_type'],
                    'is_default' => $item['is_default'],
                    'operational_model' => $item['operational_model'],
                    'logo_img' => $item['logo_img'],
                ];
                $factory_id_arr = array_column($this->_factoryStoreList,'factory_id');
                if(!in_array($item['factory_id'],$factory_id_arr)){//factory不存在
                    $this->_factoryStoreList[] = [
                        'factory_id' => $item['factory_id'],
                        'factory_name' => $item['factory_name'],
                        'factory_type' => $item['factory_type'],
                        'storeList' => [$storeInfoArr],
                    ];
                }else{//factory存在
                    foreach ($this->_factoryStoreList as &$value){
                        if($value['factory_id'] == $item['factory_id']){
                            $value['storeList'][] = $storeInfoArr;
                        }
                    }
                }
            }
        }
        $this->assign('factoryStoreList', $this->_factoryStoreList);
    }

    /**获取店长店铺列表
     */
    protected function getStoreList(){
        $model = new \common\model\UserStore();
        $config = [
            'field' => [
                'us.id user_store_id','us.user_id','us.user_name',
                's.id store_id','s.store_type','s.run_type','s.is_default','s.operational_model',
                'case s.store_type when 1 then r.logo_img when 2 then b.brand_img END as logo_img',
                'case s.store_type when 1 then r.short_name when 2 then b.name END as store_name',
                'f.id factory_id','f.name factory_name','f.type factory_type',
            ],'join' => [
                ['store s','s.id = us.store_id','left'],
                ['record r','r.id = s.foreign_id','left'],
                ['brand b','b.id = s.foreign_id','left'],
                ['factory f','f.id = us.factory_id','left'],
            ],'where' => [
                ['us.status','=',0],
                ['us.type','=',3],
                ['us.user_id','=',$this->user['id']],
                ['s.status','=',0],
                ['f.status','=',0],
                ['f.type','=',config('custom.type')],
            ],
        ];
        $storeList = $model->getList($config);
        $this->_storeList = $storeList;
    }

    //获取店铺门店列表
    protected function getStoreShopList($storeId=0){
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
        return $shopList;
    }
}