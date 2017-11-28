<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/27
 * Time: 14:52
 */

/**返回任务单签约状态-中文
 * @param $number
 * @return mixed
 */
function taskOrderSignStatu($number) {
    $arr = C('TASK_ORDER_SIGN_STATU');
    return $arr[intval($number)];
}

/**返回任务单类型-中文
 * @param $number
 * @return mixed
 */
function taskOrderType($number) {
    $arr = C('TASK_ORDER_TYPE');
    return $arr[intval($number)];
}

/**返回卖手类型
 * @param $str
 * @return string
 */
function sellerType($str){
    $returnStr = '';
    $arr =C('SELLER_TYPE');
    $tempArr = explode(',',$str);
    foreach ($tempArr as $val){
        if($val){
            $returnStr .= '[' . $arr[$val] . ']';
        }
    }
    return $returnStr;
}

/**结款方式
 * @param $number
 * @return string
 */
function paymentWay($number){
    return intval($number) == 1 ? '日结' : '活动结束当天结';
}

/**公开抢单or指派卖手
 * @param $number
 * @return string
 */
function grabOrAssigned($number){
    return intval($number) ? '指派给已收藏的卖手' : '供卖手公开抢单';
}

/**返回省-市-区县 中文
 * @param $province 省编码 $city：市编码 $area 区县编码
 * @return mixed
 */
function province_city_area($province_city_area) {
    $str = '';
    if(is_array($province_city_area)){
        $arr = C('PROVINCE_CITY_AREA');
        if($province_city_area['province']){
            $arr = $arr[$province_city_area['province']];
            $str .= $arr['province_name'];
            if($province_city_area['city']){
                $arr = $arr['city'][$province_city_area['city']];
                $str .= $arr['city_name'];
                if($province_city_area['area']){
                    $str .= $arr['area'][$province_city_area['area']];
                }
            }
        }
    }
    return $str;
}

/**返回性别-中文
 * @param $number
 * @return mixed
 */
function sex($number){
    $arr = C('SEX');
    return $arr[intval($number)];
}

/**根据级别获取邀请卖手的次数
 * @param $level    0：保留 1:荣耀级 2：金牌级 3：高级 4：普通级 5特约级
 * @return mixed 次数
 */
function getInvitesFromCompanyLevel($level){
    $arr = array(
        0,  //保留
        10, //荣耀级
        8, //金牌级
        5, //高级
        3, //普通级
        1, //5特约级
    );
    return $arr[intval($level)];
}