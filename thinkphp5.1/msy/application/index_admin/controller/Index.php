<?php
namespace app\index_admin\controller;

class Index extends \common\controller\UserBase
{
    //首页
    public function index()
    {
        $menu = new \common\lib\Menu();
        if($this->user['type']==0){//超级管理员
            $menuList = $menu->getAllDisplayMenu();
        }else{
            $menuList = $menu->getOwnDisplayMenu();
        }
        $this->assign('menu',$menuList);
        return $this->fetch();
    }
    //欢迎页
    public function welcome()
    {
        return $this->fetch();
    }
}