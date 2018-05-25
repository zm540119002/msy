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
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        return $this->fetch();
    }

    public function test()
    {
        echo  Order::addOrder();
    }
}
