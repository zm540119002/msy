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
    //城市级别
    'CITY_TYPE' => array(
        array(
            'key' => 1,
            'value' => 'A类',
        ),array(
            'key' => 2,
            'value' => 'B类',
        ),array(
            'key' => 3,
            'value' => 'C类',
        ),
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
        array('earnings'   => '收益',),//9
        array('QR_codes'   => '我的二维码',),//10
        array('forward'   => '一键分享转发',),//11
        array('forward_to_friends'   => '将本页转发微信好友',),//12
        array('goto_gift_purchase'   => '进入礼品采购',),//13
        array('apply_partner'   => '申请美悦会城市合伙人',),//14
        array('city_search'   => '申请查询',),//15
        array('next_step'   => '下一步',),//16
        array('pay_deposit'   => '支付席位订金',),//17
        array('pay_partner_fee'   => '支付资格余款',),//18
        array('pay_agent_fee'   => '支付代理商资格款',),//19
        array('group_buy_end'   => '该团已结束，重新开团',),//20
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
            'price_name'=>'商品优惠价',
            'cash_back'=>'团购返现',
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

    //快递公司'承接公司：0：自有 1：顺丰 2：圆通 3:申通 4：韵达',
    'UNDERTAKE_COMPANY'=>array(
       '0'=>'自有',
       '1'=>'顺丰',
       '2'=>'圆通',
       '3'=>'申通',
       '4'=>'韵达',
    ),

    //订单状态
    'ORDER_STATUS' => [
        '0'   => '临时',
        '1'   => '待付款',
        '2'   => '待收货',
        '3'   => '待评价',
        '4'   => '已完成',
        '5'   => '已取消',
    ],

    //订单状态 0：备货中 1：已发货 2：已送达 3:已签收 4：已取消',
    'DELIVER_STATUS' => [
        '0'   => '备货中',
        '1'   => '已发货',
        '2'   => '已送达',
        '3'   => '已签收',
        '4'   => '已取消',
    ],

    //订单类型
    /**
     * 订单类型
     * 0：临时 1:待付款 2:待收货 3:待评价 4:已完成 5:已取消',
     */
    'ORDER_TYPE' => [
        '0'   => [
            'name'=>'全部订单',
            'value'=>'0',
        ],
        '1'   => [
            'name'=>'待付款订单',
            'value'=>'1',
        ],
        '2'   => [
            'name'=>'待收货订单',
            'value'=>'2',
        ],
        '3'   => [
            'name'=>'退款订单',
            'value'=>'3',
        ],
        '4'   => [
            'name'=>'退换货订单',
            'value'=>'4',
        ],
        '5'   => [
            'name'=>'已取消订单',
            'value'=>'5',
        ],
    ],

    //订单状态
    'ORDER_CATEGORY' => [
        '0'   => '普通',
        '1'   => '团购',
    ],

);
