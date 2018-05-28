<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/28
 * Time: 13:43
 * 订单明细控制器
 */
namespace app\store\model;

use think\Model;

class OrderDetail extends Model
{
    protected $table = 'order_detail';
    protected $connection = 'db_config_factory';
    protected $pk = 'id';

    //被关联模型
    public function order()
    {
        return $this->belongsTo('Order');
    }
}