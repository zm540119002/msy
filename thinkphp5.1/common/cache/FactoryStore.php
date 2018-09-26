<?php
namespace common\cache;

class FactoryStore{
    private static $_cache_key = 'cache_store_';
    private static $_cache_key_list = 'cache_store_list_';

    /**从缓存中获取信息
     */
    public static function get($id){
        $store = cache(self::$_cache_key.$id);
        if(!$store){
            $model = new \common\model\Store();
            $config = [
                'where' => [
                    ['id','=',$id]
                ],'field' =>[
                    's.id,s.is_default,s.store_type,run_type,auth_status'
                ]
            ];
            $store = $model -> getInfo($config);
            cache(self::$_cache_key.$id, $store,config('custom.factory_cache_time'));
        }
        return $store;
    }

    /**删除缓存信息
     */
    public static function remove($id){
        cache(self::$_cache_key.$id, null);
    }

    /**从缓存中获取厂商店铺信息列表详情
     */
    public static function getList($factorId){
        $storeList = cache(self::$_cache_key_list.$factorId);
        if(!$storeList){
            $model = new \common\model\Store();
            $config = [
                'where' => [
                    ['s.factory_id','=',$factorId]
                ],'join' => [
                    ['record r','r.id = s.foreign_id','left'],
                    ['brand b','b.id = s.foreign_id','left']
                ],'field' => [
                    's.id','s.store_type','s.run_type','s.is_default','case s.store_type when 1 then r.logo_img when 2 then b.brand_img END as logo_img',
                    'case s.store_type when 1 then r.short_name when 2 then b.name END as name',
                ],
            ];
            $storeList = $model -> getList($config);
            cache(self::$_cache_key_list.$factorId, $storeList,config('custom.factory_cache_time'));
        }
        return $storeList;
    }

    /**删除缓存厂商店铺信息列表详情
     */
    public static function removeList($factorId){
        cache(self::$_cache_key_list.$factorId, null);
    }
}