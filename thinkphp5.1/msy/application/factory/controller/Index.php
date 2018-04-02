<?php
namespace app\factory\controller;

use think\Controller;

class Index extends Controller
{
    /**首页
     */
    public function index()
    {
        print_r(config('node.'));
        exit;
        return $this->fetch();
    }
}