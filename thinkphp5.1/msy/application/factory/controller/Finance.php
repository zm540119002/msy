<?php
/**
 * Created by PhpStorm.
 * User: Mr.WEI
 * Date: 2018/7/13
 * Time: 10:12
 * 财务管理控制器
 */

namespace app\factory\controller;

class Finance extends StoreBase
{

    public function index()
    {
        //财务管理首页
        return $this->fetch();
    }

    public function getApp()
    {
        return $this->fetch();
    }
}