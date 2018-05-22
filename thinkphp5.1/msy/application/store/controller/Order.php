<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/21
 * Time: 14:09
 * 订单控制器
 */
namespace app\store\controller;
use think\Controller;
use think\Db;
//use app\store\model\Store;
use app\store\model\Order as OrderModel;

class Order extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function test()
    {
        return OrderModel::addOrder();
        eixt();
        var_dump( OrderModel::get(1) );
        return OrderModel::where('order_sn', '=', '2018052100001')->select();
        //OrderModel::create(['order_sn'=>'2018052200001']);
        //$a = Db::table('msy_factory.orders')->where('order_sn', '=', '2018052100001')->select();

        return OrderModel::getLastSql();
        return dump(  OrderModel::get(1) );
    }
    /**
     * 增加订单
     *
     * 检测商品
     * 检测发货地址
     *
     *
     */
     public function addOrder()
    {
      return dump(  OrderModel::addOrder() );

    }
}