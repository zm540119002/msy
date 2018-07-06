<?php
namespace app\practitioner\controller;
/**
 * Class TwitterBase
 * @package app\user\controller
 * 店铺基础类
 */
class PractitionerBase extends \common\controller\UserBase{
    protected $practitioner = null;
    public function __construct(){
        parent::__construct();
        if($this->user){
            //获取厂商店铺详情列表
        }
    }
}