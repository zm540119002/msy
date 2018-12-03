<?php
namespace app\twitter\controller;

class Video extends TwitterBase{
    /**首页
     */
    public function index(){
        return $this->fetch();
    }
}