<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/11
 * Time: 9:36
 */

namespace app\index_admin\controller;
use think\Controller;
use think\Request ;

class Login extends Controller
{
    public function index()
    {
//        return dump(config());
        // return dump(config('menu_conf.menu_array'));
        return $this->fetch();
    }
}