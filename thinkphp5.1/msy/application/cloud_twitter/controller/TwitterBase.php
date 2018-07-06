<?php
namespace app\cloud_twitter\controller;
/**
 * Class TwitterBase
 * @package app\user\controller
 * 店铺基础类
 */
class TwitterBase extends \common\controller\UserBase{
    protected $twitter = null;
    public function __construct(){
        parent::__construct();
        if($this->user){
            //获取厂商店铺详情列表
        }
    }
}