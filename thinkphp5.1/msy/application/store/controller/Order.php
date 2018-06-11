<?php
/**
 * Created by PhpStorm.
 * User: mr.wei
 * Date: 2018/5/21
 * Time: 14:09
 * 订单控制器
 */
namespace app\store\controller;

use common\controller\UserBase;
use think\facade\Session;
use app\store\model\Order as OrderModel;

class Order extends UserBase
{
    private $order;

    public function __construct()
    {
        parent::__construct();
        if(!is_object($this->order)){
            $this->order = new OrderModel();
        }
    }

    /**
     * 增加订单
     *
     * 检测商品
     * 检测发货地址
     *
     *
     */
    public function addOrder($address_id)
    {
        return  $this->order->addOrder(Session::get('user.id'), $address_id);
    }

    //订单列表
    public function index()
    {
        $a = $this->order->orderDetail()->where(['order_id'=>112])->select();
        dump($a);
        //$this->fetch();
    }
}