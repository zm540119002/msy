<?php
namespace app\practitioner\controller;

class Index extends PractitionerBase{
    /**首页
     */
    public function index(){
        return $this->fetch();
    }
}