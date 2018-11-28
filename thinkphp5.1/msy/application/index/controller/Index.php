<?php
namespace app\index\controller;

use think\Controller;

class Index extends Controller
{
    //首页
    public function index()
    {
//        return $this->request->param('aa');
        echo 123;exit;
        return $this->fetch();
    }

    public function test()
    {
        return $this->fetch();
    }
}