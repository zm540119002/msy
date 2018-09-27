<?php
namespace common\cache;

class Factory{
    private static $_cache_key = 'cache_factory_';
    /**从缓存中获取信息
     */
    public static function get($userId,$type){
        $factoryList = cache(self::$_cache_key.$userId);
        print_r($factoryList);
        echo "-------------------"."<br>";
        if(!$factoryList){
            $modelUserFactory = new \common\model\UserFactory();
            $where = [
                ['uf.status','=',0],
                ['uf.user_id','=',$userId],
                ['f.type','=',$type],
            ];
            $field = [
                'uf.is_default','f.id','f.name',
                'r.logo_img',
            ];
            $join = [
                ['factory f','uf.factory_id = f.id','left'],
                ['record r','r.factory_id = uf.factory_id','left'],
            ];
            $factoryList = $modelUserFactory->alias('uf')->join($join)->where($where)->field($field)->select();
            $factoryList = $factoryList->toArray();
            print_r($modelUserFactory->getLastSql());exit;
        }
        cache(self::$_cache_key.$userId, $factoryList,config('custom.factory_cache_time'));
        print_r($factoryList);exit;
        return $factoryList;
    }

    /**删除缓存信息
     */
    public static function remove($userId){
        cache(self::$_cache_key.$userId, null);
    }
}