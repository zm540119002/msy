<?php
namespace app\api\controller;

class Notice extends HssBase{
    /**首页
     */
    public function index(){

        return $this->fetch();
    }

    public function notice(){
        
    }
}