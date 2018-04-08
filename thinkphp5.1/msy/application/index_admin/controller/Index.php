<?php
namespace app\index_admin\controller;

use think\Controller;
use think\Request ;

class Index extends Controller
{
    //首页
    public function index()
    {
        return $this->fetch();
    }
    //欢迎页
    public function welcome()
    {
        return $this->fetch();
    }
}
