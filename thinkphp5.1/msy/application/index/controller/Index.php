<?php
namespace app\index\controller;

use think\Controller;

class Index extends Controller
{
    //首页
    public function index()
    {
//        return $this->request->param('aa');
        return $this->fetch();
    }

    public function test()
    {
        return $this->fetch();
    }
}