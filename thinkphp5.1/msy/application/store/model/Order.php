<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/21
 * Time: 14:17
 */
namespace  app\store\model;

use think\Model;
use think\Db;

class Order extends Model
{
    // 设置当前模型对应的完整数据表名称
    protected $table = 'orders';
    // 设置当前模型的数据库连接
    protected $connection = 'db_config_factory';
    // 设置主键
    protected $pk = 'order_id';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 添加订单
     *
     * @param array $order_data
     *
     *
     */
    public function addOrder($order_data = [])
    {
        $config = [
            'order_id'=>1,
            'order_sn'=>'201805220001',
        ];
       
        return Order::where(['order_id'=>1])->find();
        $sql = "select * from msy_factory.cart a inner join msy_factory.goods b 
            on a.store_id=b.store_id and a.goods_id=b.goods_base_id
             inner join msy_factory.goods_base  c  on b.goods_base_id=c.id
             where a.user_id=1 and a.status=1";
        return Db::execute($sql);
        $sql = "insert into msy_factory.orders(*) values('*')";
        return Db::execute($sql);
    }

}