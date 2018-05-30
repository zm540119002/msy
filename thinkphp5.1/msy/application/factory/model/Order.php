<?php
/**
 * Created by PhpStorm.
 * User: Mr.wei
 * Date: 2018/5/29
 * Time: 9:39
 * 订单管理控制器
 */
namespace app\factory\model;

use think\Model;

class Order extends Model
{
    // 设置当前模型对应的完整数据表名称
    protected $table = 'orders';
    // 设置当前模型的数据库连接
    protected $connection = 'db_config_factory';
    // 设置主键
    protected $pk = 'order_id';

    public function getOrderList($store_id)
    {
        $ret = $this->alias('a')->field('*')
            ->join('address b', 'a.address_id=b.address_id', 'INNER')
            ->where(['a.']);
    }
}