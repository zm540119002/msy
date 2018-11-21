<?php
namespace common\cache;

class Store{
    private static $_cache_key = 'cache_store_';
    private static $_cache_key_current_store = 'cache_current_store_';

    /**从缓存中获取入驻厂商店铺列表
     */
    public static function get($factorId){
        $storeList = cache(self::$_cache_key.$factorId);
        if(!$storeList){
            $model = new \common\model\Store();
            $config = [
                'where' => [
                    ['s.factory_id','=',$factorId]
                ],'join' => [
                    ['record r','r.id = s.foreign_id','left'],
                    ['brand b','b.id = s.foreign_id','left'],
                ],'field' => [
                    's.id','s.store_type','s.run_type','s.is_default',
                    'case s.store_type when 1 then r.logo_img when 2 then b.brand_img END as logo_img',
                    'case s.store_type when 1 then r.short_name when 2 then b.name END as name',
                ],
            ];
            $storeList = $model->getList($config);
            cache(self::$_cache_key.$factorId, $storeList,config('custom.factory_cache_time'));
        }
        return $storeList;
    }

    /**删除缓存入驻厂商店铺列表
     */
    public static function remove($factorId){
        cache(self::$_cache_key.$factorId, null);
    }

    /**缓存当前店铺信息
     */
    public static function getCurrentStoreInfo($userId,$storeId,$storeList){
        if($userId && $storeId){
            $countStoreList = count($storeList);
            if($storeId){
                $model = new \common\model\UserStore();
                $config = [
                    'field' => [
                        'us.id user_store_id','us.user_id','us.user_name',
                        's.id store_id','s.store_type','s.run_type','s.is_default','s.operational_model',
                        's.consignee_name','s.consignee_mobile_phone','s.detail_address',
                        's.province','s.city','s.area',
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
                        ['us.user_id','=',$userId],
                        ['s.status','=',0],
                        ['s.id','=',$storeId],
                        ['f.status','=',0],
                        ['f.type','=',config('custom.type')],
                    ],
                ];
                $storeInfo = $model->getInfo($config);
            }elseif($countStoreList==1){
                $storeInfo = $storeList[0];
            }
            $storeInfo['id'] = $storeInfo['store_id'];
        }
        return $storeInfo;
    }

    /**删除当前店铺缓存信息
     */
    public static function removeCurrentStoreInfo($userId,$storeId){
        cache(self::$_cache_key_current_store.$userId.$storeId, null);
    }
}