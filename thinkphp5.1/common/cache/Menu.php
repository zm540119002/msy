<?php
namespace common\cache;

class Menu{
    private static $_cache_key = 'all_menu_ids_';

    /**从缓存中获取信息
     */
    public static function getAllMenuIds($user){
        $allMenuIds = cache(self::$_cache_key.$user['id']);
        if(!$allMenuIds){
            $menu = new \common\lib\Menu();
            if($user['type']==0){//超级管理员
                $allMenu = $menu->getAllMenu();
            }else{
                $allMenu = $menu->getOwnMenu();
            }
            $subMenu = array_column($allMenu,'sub_menu');
            $allMenuIds = [];
            foreach ($subMenu as $item) {
                $allMenuIds = array_merge($allMenuIds,array_column($item,'id'));
            }
            cache(self::$_cache_key.$user['id'], $allMenuIds);
        }
        return $allMenuIds?:[];
    }

    /**删除缓存信息
     */
    public static function removeAllMenuIds($userId){
        cache(self::$_cache_key.$userId, null);
    }
}