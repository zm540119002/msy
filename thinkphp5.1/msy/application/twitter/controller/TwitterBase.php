<?php
namespace app\twitter\controller;
/**
 * Class TwitterBase
 * @package app\user\controller
 * 店铺基础类
 */
class TwitterBase extends \common\controller\UserBase{
    protected $twitter = null;
    public function __construct(){
        parent::__construct();
        
    }
}