<?php
namespace app\store_admin\controller;

use think\Controller;
use Request;

class Goods extends Controller
{
    public function goodsManage()
    {
//        return dump(config());
        return $this->fetch();
    }

    public function hello()
    {
        return $this->fetch();
    }
}
