<?php
namespace app\index\controller;

use think\Controller;
use think\Request;
class Index extends Controller
{
    public function index()
    {
//        return dump(config());
//        return $this->request->param('aa');
        return $this->fetch();
    }
    public function test()
    {
        return $this->fetch();
    }
}
