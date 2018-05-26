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

class Goods extends Model
{
    protected $table = 'goods';
    protected $connection = 'db_config_factory';
    protected $pk = 'id';

    //相对的关联
    public function goodsBase()
    {
        return $this->belongsTo('GoodsBase');
    }

    /**
     * 检测商品是否可添加购物车
     *
     * @param $store_id
     * @param $goods_id
     * @param $number
     * @return array
     */
    public function hasGoods($store_id, $goods_id, $number)
    {
        $goods = $this->alias('a')
            ->join('msy_factory.goods_base b', 'a.goods_base_id=b.id', 'INNER')
            ->field('a.id, a.inventory, a.status, a.shelf_status, a.sale_price, b.name')
            ->where(['a.store_id'=>$store_id, 'a.id'=>$goods_id])
            ->find();
        if(is_null($goods)){
            return errorMsg('商品已不存在');
        }
        if(!$goods->status==0){
            return errorMsg('商品不存在');
        }
        if($goods->sale_price<=0.00){
            return errorMsg('【'.$goods->name.'】销售价格有误，不能购买');
        }
        if($goods->inventory<=0||$goods->inventory<$number){
            return errorMsg('【'.$goods->name.'】库存不足');
        }
        if($goods->shelf_status!==3){
            return errorMsg('【'.$goods->name.'】已下架');
        }
        return successMsg('已加入购物车');
    }

}