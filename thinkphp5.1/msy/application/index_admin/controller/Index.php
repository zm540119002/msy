<?php
namespace app\index_admin\controller;

use think\Controller;
use think\Request ;

class Index extends Controller
{
    public function index()
    {
//        return dump(config());
       // return dump(config('menu_conf.menu_array'));
        return $this->fetch();
    }

    public function welcome()
    {
        return $this->fetch();
    }
}
