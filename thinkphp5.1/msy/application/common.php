<?php
require __DIR__ . '/../../common/function/common.php';

/**检查是否登录
 */
function checkLogin(){
    $user = session('user');
    $user_sign = session('user_sign');
    if (!$user || !$user_sign) {
        return false;
    }
    if ($user_sign != data_auth_sign($user)) {
        return false;
    }
    return $user;
}

/**获取菜单
 * $all 是否显示所有菜单，默认否
 * @return array
 */
function getMenu($all=false){
    //系统菜单
    $menuSystem = config('menu.menu');
    //模块菜单
    $menuModule = config('sub_menu.menu');
    $menu = array_merge($menuSystem,$menuModule);
    if($all){
        return $menu;
    }
    foreach ($menu as &$value){
        foreach ($value['sub_menu'] as $key=>$val){
            if(!$val['display']){
                unset($value['sub_menu'][$key]);
            }
        }
    }
    return $menu;
}

/**获取系统角色
 * @return array
 */
function getRole(){
    $modelRole = new \common\model\Role();
    $response = $modelRole->where('status','=',0)->select();
    return $response->toArray()?:[];
}