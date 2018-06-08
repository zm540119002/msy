<?php
namespace common\cache;

class Factory{
    private static $_cache_key = 'cache_factory_';
    /**从缓存中获取信息
     */
    public static function get($userId){
        $factoryList = cache(self::$_cache_key.$userId);
        if(!$factoryList){
            $modelUserFactory = new \app\factory\model\UserFactory();
            $where = [
                ['uf.status','=',0],
                ['uf.user_id','=',$userId],
            ];
            $field = [
                'uf.is_default','f.id','f.name',
            ];
            $join = [
                ['factory f','uf.factory_id = f.id','left'],
            ];
            $factoryList = $modelUserFactory->alias('uf')->join($join)->where($where)->field($field)->select();
            $factoryList = $factoryList->toArray();
        }
        cache(self::$_cache_key.$userId, $factoryList,config('custom.factory_cache_time'));
        return $factoryList;
    }

    /**删除缓存信息
     */
    public static function remove($userId){
        cache(self::$_cache_key.$userId, null);
    }
}