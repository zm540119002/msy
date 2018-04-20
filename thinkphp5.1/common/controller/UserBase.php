<?php
namespace common\controller;

/**用户信息验证控制器基类
 */
class UserBase extends Base{
    protected $user = null;
    protected $loginUrl = 'index/UserCenter/login';//登录URL

    public function __construct(){
        parent::__construct();
        //判断是否登录
        $this->user = checkLogin();
        if (!$this->user) {
            if (request()->isAjax()) {
                header('HTTP/1.1 200');
                return successMsg('isAjax');
            }else{
                $this->error(config('custom.error_login'),url($this->loginUrl));
            }
        }
        $menu = new \common\lib\Menu();
        if($this->user['type']==0){//超级管理员
            $menuList = $menu->getAllDisplayMenu();
            $menuAll = $menu->getAllMenu();
        }else{
            $menuList = $menu->getOwnDisplayMenu();
            $menuAll = $menu->getOwnMenu();
        }
        $this->assign('menu',$menuList);
        $subMenu = array_column($menuAll,'sub_menu');
        $menuAll = [];
        foreach ($subMenu as $item) {
            $menuAll = array_merge($menuAll,array_column($item,'id'));
        }
        $this->assign('menuIds',$menuAll);
    }
}