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
    private $time = 0;

    public function __construct()
    {
        parent::__construct();
        if($this->time==0){
            $this->time = time();
        }
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
        //return Shop::where(['id'=>$id, 'auth_status'=>2, 'status'=>0])->count();
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
        $sql = "update msy_factory.cart set number=number+{$number}
                where store_id={$store_id} and user_id={$user_id} and goods_id={$goods_id}";// and store_type={$store_type}
        if( Db::execute($sql) ){
            return true;
        }
       $sql = "insert into msy_factory.cart(user_id, store_id, goods_id, store_type, number, create_time)  
              select {$user_id}, {$store_id}, goods_base_id, store_type, {$number}, $this->time 
              from msy_factory.goods where store_id={$store_id} and goods_base_id={$goods_id}";
        if( Db::execute($sql) ){
            return true;
        }
    }

    private function hasGoods($user_id, $store_id, $goods_id)
    {
        //检测商品
        $sql = "select * from msy_factory.store a inner join msy_factory.goods b 
                on a.id=b.store_id where a.id={$store_id} and b.goods_base_id={$goods_id}";
        return true;
    }

    public function getCartList($user_id)
    {
        //dump(Cart::get(8));
        return Db::table('msy_factory.cart')->where(['user_id'=>$user_id])->field('*')->select();
        //return Cart::where(['user_id'=>$user_id])->field('*')->select();
        //return Cart::where(['cart_id'=>8])->field('*')->select();
    }

}