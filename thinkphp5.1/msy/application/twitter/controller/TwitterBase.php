<?php
namespace app\twitter\controller;

class TwitterBase extends \common\controller\UserBase{
    protected $twitter = null;
    public function __construct(){
        parent::__construct();
    }
}