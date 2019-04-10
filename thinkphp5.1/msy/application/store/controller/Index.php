<?php
namespace app\store\controller;

class Index extends \common\controller\Base{
    /**首页
     */
    public function index(){
        if (request()->isAjax()) {
        } else {
            print_r(request()->rootUrl());
            print_r(request()->domain());
            return $this->fetch();
        }
    }
}