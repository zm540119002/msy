<?php
namespace app\purchase\controller;

class Index extends \common\controller\Base{
    /**首页
     */
    public function index(){
        return $this->fetch();
    }
}