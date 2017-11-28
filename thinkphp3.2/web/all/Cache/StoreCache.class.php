<?php
namespace web\all\Cache;

class StoreCache{
    private static $_cache_key = 'cache_store_';

    /**
     * 从缓存中获取信息
     * @param $user_id
     * @return mixed
     */
    public static function get($user_id){
        $store = S(StoreCache::$_cache_key.$user_id);
        if(!$store){
            $where = array(
                'founder_id' => $user_id,
                'status' => 0,
            );
            $store = M('store')->where($where)->find();
            S(StoreCache::$_cache_key.$user_id, $store, array('type'=>'file', 'expire'=>C('DEFAULT_EXPIRE')));
        }
        return $store;
    }

    public static function remove($user_id){
        S(StoreCache::$_cache_key.$user_id, null);
    }
}

