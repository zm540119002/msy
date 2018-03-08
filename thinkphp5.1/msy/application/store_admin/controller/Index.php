<?php
namespace app\store_admin\controller;

use think\Controller;
use Request;

class Index extends Controller
{
    public function index()
    {
        return $this->fetch();
    }

    public function hello()
    {
        return $this->fetch();
    }
}
