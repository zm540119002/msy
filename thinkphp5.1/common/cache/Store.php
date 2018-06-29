<?php
namespace common\cache;

class Store{
    private static $_cache_key = 'cache_store_';
    /**从缓存中获取信息
     */
    public static function get($userId){
        $storeList = cache(self::$_cache_key.$userId);
        if(!$storeList){
            $modelUserStore = new \app\store\model\UserStore();
            $where = [
                ['uf.status','=',0],
                ['uf.user_id','=',$userId],
            ];
            $field = [
                'uf.is_default','f.id','f.name',
            ];
            $join = [
                ['store f','uf.store_id = f.id','left'],
            ];
            $storeList = $modelUserStore->alias('uf')->join($join)->where($where)->field($field)->select();
            $storeList = $storeList->toArray();
        }
        cache(self::$_cache_key.$userId, $storeList,config('custom.store_cache_time'));
        return $storeList;
    }

    /**删除缓存信息
     */
    public static function remove($userId){
        cache(self::$_cache_key.$userId, null);
    }
}