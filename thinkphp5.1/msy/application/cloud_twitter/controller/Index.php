<?php
namespace app\cloud_twitter\controller;

class Index extends TwitterBase{
    /**首页
     */
    public function index(){
        return $this->fetch();
    }
}