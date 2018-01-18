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

/**检查是否代理商
 * @param $mobilePhone
 * @return bool true:是 false:否
 */
function checkAgentByMobilePhone($mobilePhone){
    $modelAgent = D('Agent');
    $where = array(
        'a.mobile_phone' => $mobilePhone,
    );
    $count = $modelAgent->where($where)->count('1');
    return $count?true:false;
}