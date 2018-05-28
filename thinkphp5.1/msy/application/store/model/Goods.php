<?php
/**
 * Created by PhpStorm.
 * User: mr.wei
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
        $goods = $this->field('id, inventory, status, shelf_status, sale_price, name')
            ->where(['store_id'=>$store_id, 'id'=>$goods_id])
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

    /**
     * 获取店铺商品列表
     * @param $store_id
     * @return \think\Paginator
     */
    public function getList($store_id)
    {
        return $this->field('id, name, retail_price, thumb_img, store_id, sale_price')
                ->where(['store_id'=>$store_id, 'shelf_status'=>3, 'status'=>0])
                ->where('inventory', '>', 0)
                ->paginate(2);
    }

    /**
     * 获取单个店铺商品明细
     * @param $store_id
     * @param $goods_id
     * @return array|\PDOStatement|string|\think\Collection
     */
    public function getDetail($store_id, $goods_id)
    {
        return $this->field('id, name, thumb_img, store_id, sale_price')
                ->where(['id'=>$goods_id, 'store_id'=>$store_id, 'shelf_status'=>3, 'status'=>0])
                ->select();
    }

}