<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/29
 * Time: 11:01
 * 订单地址保存
 */
namespace app\store\model;

use think\Model;

class AddressStatic extends Model
{
    protected $table = 'address_static';
    protected $connection = 'db_config_factory';
    protected $pk = 'order_id';

    //关联模型
    public function order()
    {
        return $this->belongsTo('Order');
    }

}