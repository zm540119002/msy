<?php
namespace common\cache;

class Menu{
    private static $_cache_key = 'cache_menu_';

    /**从缓存中获取信息
     */
    public static function get($user_id){
        $menu = S(self::$_cache_key.$user_id);
        if(!$menu){
            $where = array(
                'user_id' => $user_id,
                'status' => 0,
                'auth_status' => 1,
            );
            $modelMenu = D('Business/Menu');
            $menu = $modelMenu->selectMenu($where);
            $menu = $menu[0];
            S(self::$_cache_key.$user_id, $menu, array('type'=>'file', 'expire'=>C('DEFAULT_EXPIRE')));
        }
        return $menu;
    }

    /**从缓存中获取信息
     */
    public static function getByMobilePhone($mobilePhone){
        $menu = S(self::$_cache_key.$mobilePhone);
        if(!$menu){
            $where = array(
                'mobile_phone' => $mobilePhone,
                'status' => 0,
                'auth_status' => 1,
            );
            $modelMenu = D('Menu');
            $menu = $modelMenu->selectMenu($where);
            $menu = $menu[0];
            S(self::$_cache_key.$mobilePhone, $menu, array('type'=>'file', 'expire'=>C('DEFAULT_EXPIRE')));
        }
        return $menu;
    }

    /**删除缓存信息
     */
    public static function remove($id){
        S(self::$_cache_key.$id, null);
    }

    /**删除缓存信息
     */
    public static function removeByMobilePhone($mobilePhone){
        S(self::$_cache_key.$mobilePhone, null);
    }
}

