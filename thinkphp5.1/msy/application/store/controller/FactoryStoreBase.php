<?php
namespace app\store\controller;

class FactoryStoreBase extends \common\controller\UserBase{
    protected $_storeList = null;
    protected $_store = null;
    protected $_factoryStoreList = null;
    protected $_defaultDialog = null;

    public function __construct(){
        parent::__construct();
    }

    /**获取店铺列表
     */
    protected function getStoreList(){
        $model = new \common\model\UserStore();
        $config = [
            'field' => [
                'us.id user_store_id','us.user_id','us.user_name','us.store_id','us.factory_id','us.type user_store_type',
                's.store_type','s.run_type','s.is_default','s.operational_model',
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
                ['f.type','=',2],
            ],
        ];
        $storeList = $model->getList($config);
        $this->_storeList = $storeList;
    }

    /**获取店家店铺列表
     */
    protected function getFactoryStoreList(){
        $this->getStoreList();
        $storeListCount = count($this->_storeList);
        if($storeListCount>0){
            foreach ($this->_storeList as $item) {
                $storeInfoArr = [
                    'store_id' => $item['store_id'],
                    'store_name' => $item['store_name'],
                    'logo_img' => $item['logo_img'],
                    'store_type' => $item['store_type'],
                    'run_type' => $item['run_type'],
                    'operational_model' => $item['operational_model'],
                    'is_default' => $item['is_default'],
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
    }

    /**获取当前店铺
     */
    protected function getStoreInfo($storeId=0){
        if($storeId){
            $model = new \common\model\Store();
            $config = [
                'field' => [
                    's.id','s.store_type','s.run_type','s.is_default','s.operational_model',
                    's.consignee_name','s.consignee_mobile_phone','s.province','s.city','s.area','s.detail_address',
                    'case s.store_type when 1 then r.logo_img when 2 then b.brand_img END as logo_img',
                    'case s.store_type when 1 then r.short_name when 2 then b.name END as store_name',
                    'f.id factory_id','f.name','f.type',
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
            $this->_store = $storeInfo;
        }elseif(count($this->_storeList)==1){
            $this->_store = $this->_storeList[0];
        }elseif(count($this->_storeList)>1){
            $this->_defaultDialog = true;
        }
    }
}