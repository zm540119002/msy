<?php
namespace app\factory\controller;

use think\Controller;
use Request;

class Index extends Controller
{
    public function index()
    {
        return $this->fetch();
    }
}