<?php
namespace app\index\controller;

use think\Controller;

class Index extends Controller
{
    public function index()
    {
//        return dump(config());
//        return $this->request->param('aa');
        return $this->fetch();
    }
}
