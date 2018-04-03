<?php
namespace app\index_admin\controller;

use think\Controller;
use think\Request ;

class Index extends Controller
{
    public function index()
    {
        return $this->fetch();
    }

    public function welcome()
    {
        return $this->fetch();
    }
}
