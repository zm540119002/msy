<?php
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
