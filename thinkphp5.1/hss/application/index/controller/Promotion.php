<?php
namespace app\index\controller;

/**
 * 促销控制器
 */
class Promotion extends \common\controller\Base{

    public function index(){
        return $this->fetch();
    }
}