<?php
namespace app\index_admin\controller;

class Index extends \common\controller\UserBase
{
    //首页
    public function index()
    {
        if($this->user['type']==0){//超级管理员
            $menu = getMenu();
            $this->assign('menu',$menu);
        }else{
        }
        $auth = new \common\lib\Auth();
        $allMenu = $auth->test();
        foreach ($allMenu as $value){
            print_r($value);
            echo '<br/>';
        }
        exit;
        return $this->fetch();
    }
    //欢迎页
    public function welcome()
    {
        return $this->fetch();
    }
}