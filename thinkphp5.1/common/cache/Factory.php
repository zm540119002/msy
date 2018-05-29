<?php
namespace common\cache;

class Factory{
    private static $_cache_key = 'cache_factory_';

    /**从缓存中获取信息
     */
    public static function getByUserId($userId){
        $factory = cache(self::$_cache_key.$userId);
        if(!$factory){
            $model = new \app\factory\model\UserFactory();
            $file = [
                'u.id,u.factory_id,u.is_default,f.name'
            ];
            $join =[
                ['factory f','f.id = u.factory_id'],
            ];
            if($factoryCount > 1){
                $_where = [
                    ['u.user_id','=',$userId],
                    ['u.is_default','=',1],
                ];

                if(!$factoryInfo){
                    $this->success('你有多家厂商入住，请选择一家', 'Index/index');
                }
            }elseif ($factoryCount == 1){
                $_where = [
                    ['u.user_id','=',$userId],
                ];
                $factoryInfo = $model -> getInfo($where_new,$file,$join);
            }elseif (!$factoryCount){
                $this->success('没有产商入住，请入住', 'Deploy/register');
            }
            $factory = $model-> getInfo($_where,$file,$join);
            cache(self::$_cache_key.$userId, $factory,config('custom.factory_cache_time'));
        }
        return $factory;
    }

    /**删除缓存信息
     */
    public static function removeByUserId($userId){
        cache(self::$_cache_key.$userId, null);
    }
}