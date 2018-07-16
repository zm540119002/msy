<?php
namespace app\mall\controller;

class MallBase extends \common\controller\UserBase{
    protected $twitter = null;
    public function __construct(){
        parent::__construct();
    }
}