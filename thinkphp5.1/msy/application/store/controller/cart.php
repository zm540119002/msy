<?php
/**
 * Created by PhpStorm.
 * User: Mr.wei
 * Date: 2018/5/17
 * Time: 10:01
 * 购物车控制器
 */
namespace app\store\controller;

use common\controller\UserBase;
use think\facade\Session;
use app\store\model\Cart as CartModel;
use app\store\model\Address;
use app\store\model\Goods;

/**
 * 购物车控制器
 */
class Cart extends UserBase
{
    private $cart;
    private $user_id = '';

    public function __construct()
    {
        parent::__construct();
        if(!is_object($this->cart)){
            $this->cart = new CartModel;
        }
        if(!$this->user_id){
            $this->user_id = Session::get('user.id');
        }
    }

    //购物车页面列表
    public function index()
    {
        $address =new Address;
        $ret = $address->getAddress($this->user_id, true);
        if($ret['status']==1){
            $this->assign( 'address',  $ret['data']);
        }else{
            $this->assign( 'address',  []);
        }
        $cart = $this->cart->getCartList($this->user_id);
        if($cart['status']==1){
            $this->assign('error_info', '');
        }else{
            $this->assign('error_info', $cart['info']);
        }
        $this->assign( 'cartList',  $cart['data']);
        return $this->fetch();
    }

    /**
     * 商品加入购物车
     * @param $store_id
     * @param $goods_id
     * @param $number
     * @return array|bool
     */
    public function addGoods($store_id, $goods_id, $number)
    {   //判断商品是否可加入购物车
        $goods = new Goods;
        $ret = $goods->hasGoods($store_id, $goods_id, $number);
        if($ret['status']!==1){
            return $ret;
        }
        return $this->cart->addGoods($this->user_id, $store_id, $goods_id, $number);
    }

    public function deleteGoods($goods_id)
    {
        return $this->cart->deleteGoods($this->user_id, $goods_id);
    }

    public function test()
    {
        $order = new \app\store\model\Order;
        dump ( $order->setOrderUnpack(30) );
    }

}