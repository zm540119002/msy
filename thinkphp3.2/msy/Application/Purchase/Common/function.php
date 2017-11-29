<?php
/**
 * @param $goodsInfo 商品信息
 * @param string $level 用户级别
 * @return mixed
 */
function getGoodsPirceByUserLevel($goodsInfo,$level = 0){
    switch ($level){
        case 0:
            return $goodsInfo['price'];
        case 1:
            return $goodsInfo['vip_price'];
        case 2:
            return $goodsInfo['senior_vip_price'];
        case 3:
            return $goodsInfo['gold_vip_price'];
        default:
            return $goodsInfo['special_price']?:0;
    }
}

/**获取级别星级中文
 * @param $num
 * @return string
 */
function getStarCN($num){
    return $num?C('LEVEL_STAR')[$num]:'保留';
}

/**订单支付状态
 */
function getPayStatusCN($num){
    return C('PAY_STATUS')[$num]?:'保留';
}

/**订单状态
 */
function orderStatus($num){
    return C('ORDER_STATUS')[$num]?:'保留';
}