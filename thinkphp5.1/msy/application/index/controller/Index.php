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
        echo __FILE__;
        echo  realpath(dirname(__FILE__) . '/../application');
        exit;
        $image = \common\component\image\Image::open('./image.png');
        print_r($image);
        exit;
        return $this->fetch();
    }
}
