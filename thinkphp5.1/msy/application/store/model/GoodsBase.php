<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/26
 * Time: 11:18
 * 商品模型
 */
namespace app\store\model;

use think\Model;

class GoodsBase extends Model
{
    protected $table = 'goods_base';
    protected $connection = 'db_config_factory';
    protected $pk = 'id';

    //关联定义
    public function goods()
    {
        return $this->hasMany('Goods', 'goods_base_id', 'id');
    }

}