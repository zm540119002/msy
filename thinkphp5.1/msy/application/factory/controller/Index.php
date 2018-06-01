<?php
namespace app\factory\controller;
use common\controller\UserBase;
class Index extends FactoryBase
{
    /**首页
     */
    public function index()
    {
        return $this->fetch();
    }
}