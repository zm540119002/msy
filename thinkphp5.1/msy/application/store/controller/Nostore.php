<?php
/**
 * 处理店铺不存在控制器
 *
 */
namespace app\store\controller;
use app\store\model\Order;

use think\Controller;

class Nostore extends Controller
{

    public function index()
    {
        return $this->fetch();
    }

}
