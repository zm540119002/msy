<?php
/**
 * Created by PhpStorm.
 * User: Mr.wei
 * Date: 2018/5/17
 * Time: 11:31
 * 购物车模型
 */
namespace app\store\model;

use think\Model;

class Cart extends Model {
    // 设置当前模型对应的完整数据表名称
    protected $table = 'cart';
    // 设置当前模型的数据库连接
    protected $connection = 'db_config_factory';
    // 设置主键
    protected $pk = 'cart_id';
    
    /**
     * 增加购物车商品
     * @param $user_id
     * @param $store_id
     * @param $goods_id
     * @param $number
     * @return array
     */
    public function addGoods($user_id, $store_id, $goods_id, $number)
    {
        $cart = $this->where(['user_id'=>$user_id, 'store_id'=>$store_id, 'goods_id'=>$goods_id])->find();
        if(is_null($cart)){
            $ret = $this->create([
                'user_id' => $user_id,
                'store_id' => $store_id,
                'goods_id' => $goods_id,
                'number' => $number,
                'create_time' => time(),
            ], ['user_id', 'store_id', 'goods_id', 'number', 'create_time']);
        }else{
            $ret = $this->where(['cart_id'=>$cart->cart_id])->setInc('number', $number);
        }
        if($ret){
            return successMsg('添加购物车成功');
        }
        return errorMsg('添加购物车失败');
    }

    /**
     * 获取购物车商品列表
     * @param $user_id
     * @return array|\PDOStatement|string|\think\Collection
     */
    public function getCartList($user_id)
    {
        $ret = $this->alias('a')
            ->join('goods b', 'a.goods_id=b.id', 'LEFT')
            ->field('a.goods_id, a.number, a.store_id, b.id, b.sale_price, b.inventory, b.name, b.thumb_img,
            b.shelf_status, b.status')
            ->where(['a.user_id'=>$user_id])
            ->select();
        if(count($ret)<=0){
            return errorMsg('亲，您的购物车是空的！');
        }
        foreach($ret as $v){
            if($v['goods_id']!=$v['id']){
                return errorMsg("【{$v['name']}】商品已下架！", ['data'=>$ret]);
            }
            if($v['sale_price']<=0.00){
                return errorMsg("【{$v['name']}】销售价格有误，不能购买！", ['data'=>$ret]);
            }
            if($v['number']<=0){
                return errorMsg("【{$v['name']}】购买数量不能低于一件！", ['data'=>$ret]);
            }
            if($v['inventory']==0){
                return errorMsg("【{$v['name']}】的库存为零，不能购买！", ['data'=>$ret]);
            }
            if($v['number']>$v['inventory']){
                return errorMsg("【{$v['name']}】的购买量大于库存，不能购买！", ['data'=>$ret]);
            }
            if($v['shelf_status']!=3){
                return errorMsg("【{$v['name']}】已下架，不能购买！", ['data'=>$ret]);
            }
            if($v['status']==1){
                return errorMsg("【{$v['name']}】已下架，不能购买！", ['data'=>$ret]);
            }
        }
        return successMsg('购物车商品列表', ['data'=>$ret]);
    }

    /**
     * 删除购物车商品
     * @param $user_id
     * @param $goods_id
     * @return array
     */
    public function deleteGoods($user_id, $goods_id)
    {
        $ret = $this->where(['user_id'=>$user_id, 'goods_id'=>$goods_id])->delete();
        if($ret){
            return successMsg('删除成功');
        }
        return errorMsg('删除失败');
    }
    
}