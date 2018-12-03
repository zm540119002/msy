<?php
namespace app\twitter\controller;

class TwitterBase extends \common\controller\Base{
    protected $twitter = null;
    public function __construct(){
        parent::__construct();
    }
}