<?php
namespace app\purchase\controller;

class Index extends Base{
    /**首页
     */
    public function index(){
        return $this->fetch();
    }
}