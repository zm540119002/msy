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
use think\Db;

class Cart extends Model {
    // 设置当前模型对应的完整数据表名称
    protected $table = 'cart';
    // 设置当前模型的数据库连接
    protected $connection = 'db_config_factory';
    // 设置主键
    protected $pk = 'cart_id';

    public function __construct()
    {
        parent::__construct();
    }

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
     *
     * @param int $user_id 用户ID
     * @param int $store_id 店铺ID
     * @param int $goods_id 商品ID
     * @param int $number 购买数量
     * @return boolean
     */
    public function addGoods($user_id, $store_id, $goods_id, $number)
    {
        $goods = $this->hasGoods($store_id, $goods_id, $number);
        if($goods['status']!==1){
            return $goods;
        }
        $cart = Db::table('msy_factory.cart')
            ->where(['user_id'=>$user_id, 'store_id'=>$store_id, 'goods_id'=>$goods_id])->find();
        if( is_array($cart) ){
            $ret = $this->where(['cart_id'=>$cart['cart_id']])->setInc('number', $number);
        }else{
            $ret = static::create([
                'user_id' => $user_id,
                'store_id' => $store_id,
                'goods_id' => $goods_id,
                'number' => $number,
                'create_time' => time(),
            ], ['user_id', 'store_id', 'goods_id', 'number', 'create_time']);
        }
        if($ret){
            return successMsg('添加购物车成功');
        }
        return errorMsg('添加购物车失败');
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
        $goods = Db::table('msy_factory.goods')->alias('a')
            ->join('msy_factory.goods_base b', 'a.goods_base_id=b.id', 'INNER')
            ->field('a.id, a.inventory, a.status, a.shelf_status, a.sale_price, b.name')
            ->where(['a.store_id'=>$store_id, 'a.id'=>$goods_id])
            ->find();
        if(!$goods||!is_array($goods)||count($goods)<=0){
            return errorMsg('商品已不存在');
        }
        if(!$goods['status']==0){
            return errorMsg('商品不存在');
        }
        if($goods['sale_price']<=0.00){
            return errorMsg('【'.$goods['name'].'】销售价格有误，不能购买');
        }
        if($goods['inventory']<=0||$goods['inventory']<$number){
            return errorMsg('【'.$goods['name'].'】库存不足');
        }
        if($goods['shelf_status']!==3){
            return errorMsg('【'.$goods['name'].'】已下架');
        }
        return successMsg('已加入购物车');
    }

    public function getCartList($user_id)
    {
        return Db::table('msy_factory.cart')->alias('a')
                ->join('msy_factory.goods_base b ', 'a.goods_id=b.id', 'LEFT')
                ->where(['a.user_id'=>$user_id])->field('a.*, b.thumb_img')->select();
    }
    
}