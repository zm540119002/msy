<?php
namespace app\index\controller;

use think\Controller;
use Request;

class Index extends Controller
{
    public function index()
    {
//        return dump(config());
        return $this->fetch();
    }
}
