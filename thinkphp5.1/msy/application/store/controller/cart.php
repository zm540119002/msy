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

/**
 * 购物车控制器
 */
class Cart extends UserBase
{
    private $cart;

    public function __construct()
    {
        parent::__construct();
        if(!is_object($this->cart)){
            $this->cart = new CartModel();
        }
    }

    //购物车页面列表
    public function index()
    {
        $this->assign( 'cartList', $this->cart->getCartList(Session::get('user.id')) );
        return $this->fetch();
    }

    /**
     * 商品加入购物车
     *
     * @param int $store_id 店铺ID
     * @param int $goods_id 商品ID
     * @param int $number 购买数量
     * @return boolean
     */
    public function addGoods($store_id, $goods_id, $number)
    {
        return $this->cart->addGoods(Session::get('user.id'), $store_id, $goods_id, $number);
    }

}