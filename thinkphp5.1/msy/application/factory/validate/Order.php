<?php
/**
 * Created by PhpStorm.
 * User: Mr.wei
 * Date: 2018/7/7
 * Time: 9:54
 */
namespace app\factory\validate;

use think\Validate;

class Order extends Validate
{
    protected $rule = [
        'order_id'  =>  'require|integer|token',
        'store_id' => 'require|integer',
    ];

    protected $message = [
        'order_id.require' => '订单编号不存在',
        'order_id.integer' => '订单编号错误',
        'store_id.require' => '店铺编号不存在',
        'store_id.integer' => '店铺编号错误',
    ];

}