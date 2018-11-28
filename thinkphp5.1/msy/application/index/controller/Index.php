<?php
namespace app\index\controller;

use think\Controller;

class Index extends \common\controller\Base{
    //首页
    public function index(){
        return $this->fetch();
    }
}