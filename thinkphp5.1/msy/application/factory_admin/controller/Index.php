<?php
namespace app\factory_admin\controller;

use think\Controller;

class Index extends Controller
{
    public function index()
    {
        return $this->fetch();
    }

    public function welcome() {
        return "hello api-admin";
    }
}
