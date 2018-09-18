<?php
namespace app\mall\controller;

class Index extends MallBase{

    public function index(){
        
        return $this->fetch();
    }

    public function getPerson()
    {
        return $this->fetch();
    }

    public function set()
    {
        return $this->fetch();
    }

}