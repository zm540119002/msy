<?php
/**
 * Created by PhpStorm.
 * User: mr.wei
 * Date: 2018/5/30
 * Time: 11:01
 * 订单拆分模块
 */
namespace app\store\model;

use think\Model;

class OrderUnpack extends Model
{
    protected $table = 'order_unpack';
    protected $connection = 'db_config_factory';
    protected $pk = 'order_id';

    /**
     * 相对关联模型
     * @return \think\model\relation\BelongsTo
     */
    public function order()
    {
        return $this->belongsTo('Order');
    }
    
}