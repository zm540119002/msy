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
        echo realpath('../../image.png');
        exit;
        $image = \think\Image::open('./image.png');
        print_r($image);
        exit;
        return $this->fetch();
    }
}
