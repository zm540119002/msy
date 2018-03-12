<?php
namespace app\store_admin\controller;

use think\Controller;
use think\facade\Request ;

class Index extends Controller
{
    public function index()
    {
//        return dump(config());
       // return dump(config('menu_conf.menu_array'));
        return $this->fetch('error/error',['title' => '没有操作权限','msg' => '抱歉，您没有操作该页面的权限！']);
    }

    public function welcome()
    {
        return $this->fetch();
    }
}
