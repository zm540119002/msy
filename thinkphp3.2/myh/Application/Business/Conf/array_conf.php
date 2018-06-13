<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/10/18
 * Time: 17:23
 */
return [
    //订单状态
    'ORDER_STATUS' => [
        '0'   => '临时',
        '1'   => '待付款',
        '2'   => '待收货',
        '3'   => '待评价',
        '4'   => '已完成',
        '5'   => '已取消',
    ],

    //物流状态
    'LOGISTICS_STATUS' => [
        '0'   => '备货中',
        '1'   => '已发货',
        '2'   => '已送达',
        '3'   => '已签收',
        '4'   => '已取消',
    ],
    
    //评分数组
    'COMMENT_ARRAY'=>[
        '5'=>array(
            'score'=>5,
            'num'=>0,
            'percent'=>0
        ),
        '4'=>array(
            'score'=>4,
            'num'=>0,
            'percent'=>0
        ),
        '3'=>array(
            'score'=>3,
            'num'=>0,
            'percent'=>0
        ),
        '2'=>array(
            'score'=>2,
            'num'=>0,
            'percent'=>0
        ),
        '1'=>array(
            'score'=>1,
            'num'=>0,
            'percent'=>0
        ),
    ]
];
