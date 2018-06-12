<?php
namespace common\cache;

class Shop{
    private static $_cache_key = 'cache_shop_';
    private static $_cache_key_list = 'cache_shop_list_';

    /**从缓存中获取信息
     */
    public static function get($id){
        $store = cache(self::$_cache_key.$id);
        if(!$store){
            $model = new \app\store\model\Shop();
            $where = [
                'status' => 0,
                'id' => $id,
            ];
            $file = [
                'id,is_default,shop_type,run_type,auth_status'
            ];
            $store = $model -> getInfo($where,$file);
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
            $model = new \app\store\model\Shop();
            $storeList = $model -> getShopList($factorId);
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