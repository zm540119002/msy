<?php
namespace app\index_admin\controller;

class Index extends \common\controller\UserBase
{
    //首页
    public function index()
    {
        $auth = new \common\lib\Auth();
        if($this->user['type']==0){//超级管理员
            $menu = $auth->getAllDisplayMenu();
        }else{
            $menu = $auth->getOwnDisplayMenu();
        }
        $this->assign('menu',$menu);
        return $this->fetch();
    }
    //欢迎页
    public function welcome()
    {
        return $this->fetch();
    }
}