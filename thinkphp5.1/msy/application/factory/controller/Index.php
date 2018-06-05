<?php
namespace app\factory\controller;
use common\controller\UserBase;
class Index extends UserBase
{
    /**首页
     */
    public function index()
    {
        return $this->fetch();
    }
}