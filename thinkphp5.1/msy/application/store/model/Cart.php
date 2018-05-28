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
     * 判断用户是否能购买此商品
     *
     * @param int $user_id 用户ID
     * @param int $store_id 店铺ID
     * @return boolean
     */
    public  function hasCart($user_id, $store_id)
    {
        return Db::table('user_factory')->where(['user_id'=>$user_id])->column('factory_id', 'id');
    }

    /**
     *根据店铺ID获取商品列表
     *
     * @param int $id  店铺ID
     * @return  array
     */
    public function getGoodsList($id)
    {
        return Db::table('msy_factory.goods_base')->alias('a')
            ->field('a.id, a.name, a.retail_price, a.thumb_img')
            ->join('msy_factory.goods b', 'a.id=b.goods_base_id', 'LEFT')
            ->where(['a.factory_id'=>$id])
            ->select();
    }

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
            $ret = static::create([
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
        return $this->alias('a')
                ->join('goods b ', 'a.goods_id=b.id', 'LEFT')
                ->where(['a.user_id'=>$user_id])->field('a.*, b.name, b.thumb_img')->select();
    }

    public function deleteGoods($user_id, $goods_id)
    {
        return $this->where(['user_id'=>$user_id, 'goods_id'=>$goods_id])->delete();
    }
    
}