<?php
namespace app\index_admin\controller;

use think\Controller;
use think\Request ;

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
