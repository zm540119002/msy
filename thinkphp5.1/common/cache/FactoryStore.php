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
                    ['factory_id','=',$factorId]
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