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

/**代理类型
 */
function agentType($agentTypeList,$num){
    return in_array($num,array_column($agentTypeList,'type'))?'active':'';
}

/**检查是否代理商
 * @param $mobilePhone
 * @return bool true:是 false:否
 */
function checkIsAgentByMobilePhone($mobilePhone){
    $modelAgent = D('Agent');
    $where = array(
        'a.auth_status' => 1,
        '_complex' => array(
            'a.mobile_phone' => $mobilePhone,
            'u.mobile_phone' => $mobilePhone,
            '_logic' => 'or',
        ),
    );
    $join = array(
        ' left join common.user u on u.id = a.user_id ',
    );
    $count = $modelAgent->alias('a')->join($join)->where($where)->count('1');
    return $count?true:false;
}

/**购物车统计
 * @param $userId
 * @return int
 */
function cartCountByUserId($userId){
    $modelCart = D('Cart');
    if($userId){//已登录
        $where = array(
            'ct.user_id' => $userId,
        );
        $cart = $modelCart->selectCart($where);
    }else{//未登录
        $cart = unserialize(cookie('cart'));
    }
    $cartCount = 0;
    foreach ($cart as $value){
        $cartCount += $value['num'];
    }
    return $cartCount;
}
