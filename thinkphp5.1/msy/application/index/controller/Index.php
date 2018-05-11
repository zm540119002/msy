<?php
namespace app\index\controller;

use think\Controller;

class Index extends Controller
{
    public function index()
    {
//        return dump(config());
//        return $this->request->param('aa');
        echo $this->request->domain();
        exit;
        return $this->fetch();
    }
    public function test()
    {
        return $this->fetch();
    }
}
