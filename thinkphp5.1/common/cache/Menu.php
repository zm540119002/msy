<?php
namespace common\cache;

class Menu{
    private static $_cache_key = 'all_display_menu_';
    private static $_cache_key_2 = 'all_menu_';

    /**从缓存中获取信息
     */
    public static function getAllDisplayMenu($user){
        $allDisplayMenu = cache(self::$_cache_key.$user['id']);
        if(!$allDisplayMenu){
            $menu = new \common\lib\Menu();
            if($user['type']==0){//超级管理员
                $allDisplayMenu = $menu->getAllDisplayMenu();
            }else{
                $allDisplayMenu = $menu->getOwnDisplayMenu();
            }
            cache(self::$_cache_key.$user['id'], $allDisplayMenu);
        }
        return $allDisplayMenu?:[];
    }

    /**删除缓存信息
     */
    public static function removeAllDisplayMenu($userId){
        cache(self::$_cache_key.$userId, null);
    }

    /**从缓存中获取信息
     */
    public static function getAllMenu($user){
        $allMenuIds = cache(self::$_cache_key_2.$user['id']);
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
            cache(self::$_cache_key_2.$user['id'], $allMenuIds);
        }
        return $allMenuIds?:[];
    }

    /**删除缓存信息
     */
    public static function removeAllMenu($userId){
        cache(self::$_cache_key_2.$userId, null);
    }
}