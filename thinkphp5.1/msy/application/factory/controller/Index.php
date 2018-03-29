<?php
namespace app\factory\controller;

use think\Controller;

class Index extends Controller
{
    /**首页
     */
    public function index()
    {
        return $this->fetch();
    }
}