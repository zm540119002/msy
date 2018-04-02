<?php
namespace app\index_admin\controller;

use think\Controller;
use think\Request ;

class Index extends Controller
{
    public function index()
    {
        return $this->fetch();
//        return $this->fetch('template/base_admin.html');
    }

    public function welcome()
    {
        return $this->fetch();
    }
}
