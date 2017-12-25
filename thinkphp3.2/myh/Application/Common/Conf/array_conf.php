<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/10/18
 * Time: 17:23
 */
return array(
    //级别星级
    'LEVEL_STAR' => array(
        '1'   => '一星',
        '2'   => '二星',
        '3'   => '三星',
        '4'   => '四星',
        '5'   => '五星',
    ),
    //底部购物车配置
    'FOOTER_CART_MENU' => array(
        array('share'   => '分享',),//0
        array('shopping_basket'   => '购物篮',),//1
        array('amount'   => '总金额',),//2
        array('add_cart'   => '加入购物车',),//3
        array('buy_now'   => '立即购买',),//4
        array('clearing_now'   => '立即结算',),//5
        array('pay_now'   => '立即支付',),//6
        array('determine_order'   => '确定订单',),//7
        array('initiate_group_buy'   => '支付并发起微团购',),//8
    ),
    //商品购买类型
    'BUY_TYPE'=>array(
        '1'=>array(
            'buy_type' =>'1',
            'name'=>'优惠价产品',
            'price_name'=>'商品优惠价',
            'commission'=>'提成',
            'big_order_price'=>'大宗采购价',
            'small_order_price'=>'小宗采购价'
        ),
        '2'=>array(
            'buy_type' =>'2',
            'name'=>'微团购',
            'price_name'=>'微团价',
            'commission'=>'提成',
        ),
        '3'=>array(
            'buy_type' =>'3',
            'name'=>'城市合伙人',
            'price_name'=>'城市合伙人价',
            'commission'=>'提成'
        ),
        '4'=>array(
            'buy_type' =>'4',
            'name'=>'代理商',
            'price_name'=>'代理商价',
            'commission'=>'提成'
        ),
        '5'=>array(
            'buy_type' =>'5',
            'name'=>'特价产品',
            'price_name'=>'特价',
            'commission'=>'提成'
        ),
    ),
);
