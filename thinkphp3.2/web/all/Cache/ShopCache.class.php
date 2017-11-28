<?php
namespace web\all\Cache;

class ShopCache{
    private static $_cache_key = 'cache_shop_';

    /**
     * 从缓存中获取信息
     * @param $company_id
     * @return mixed
     */
    public static function get($company_id){
        $shop = S(ShopCache::$_cache_key.$company_id);
        if(!$shop){
            $where = array(
                'company_id' => $company_id,
                'type' => 0,
                'status' => 0,
            );
            $shop = M('shop')->where($where)->find();
            S(ShopCache::$_cache_key.$company_id, $shop, array('type'=>'file', 'expire'=>C('DEFAULT_EXPIRE')));
        }
        return $shop;
    }

    public static function remove($company_id){
        S(ShopCache::$_cache_key.$company_id, null);
    }
}

