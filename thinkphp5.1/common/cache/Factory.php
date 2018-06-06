<?php
namespace common\cache;

class Factory{
    private static $_cache_key = 'cache_factory_';
    /**从缓存中获取信息
     */
    public static function get($factoryId){
        $factory = cache(self::$_cache_key.$factoryId);
        if(!$factory){
            $modelFactory = new \app\factory\model\Factory();
            $where = [
                ['status','=',0],
            ];
            $field = [
                'id','name',
            ];
            if(is_array($factoryId)){
                $where[] = ['id','in',$factoryId];
                $factory = $modelFactory->getList($where,$field);
            }else{
                $where[] = ['id','=',$factoryId];
                $factory = $modelFactory->getInfo($where,$field);
                cache(self::$_cache_key.$factoryId, $factory,config('custom.factory_cache_time'));
            }
        }
        return $factory;
    }

    /**删除缓存信息
     */
    public static function remove($factoryId){
        cache(self::$_cache_key.$factoryId, null);
    }
}