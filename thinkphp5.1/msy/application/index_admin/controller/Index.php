<?php
namespace app\index_admin\controller;

class Index extends \common\controller\UserBase
{
    //首页
    public function index()
    {
        $menu = config('menu.menu');
        $this->assign('menu',$menu);
        return $this->fetch();
    }
    //欢迎页
    public function welcome()
    {
        return $this->fetch();
    }
}
