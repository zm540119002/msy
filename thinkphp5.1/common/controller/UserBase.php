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
            $allDisplayMenu = $menu->getAllDisplayMenu();
            $allMenu = $menu->getAllMenu();
        }else{
            $allDisplayMenu = $menu->getOwnDisplayMenu();
            $allMenu = $menu->getOwnMenu();
        }
        $this->assign('allDisplayMenu',$allDisplayMenu);
        $subMenu = array_column($allMenu,'sub_menu');
        $allMenu = [];
        foreach ($subMenu as $item) {
            $allMenu = array_merge($allMenu,array_column($item,'id'));
        }
        $this->assign('allMenuIds',$allMenu?:[]);
    }
}