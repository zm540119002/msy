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
function onOffLine($number) {
    $arr = C('ON_OFF_LINE');
    return $arr[intval($number)];
}

function formatImg($str){
    $arr = explode(',',$str);
    $str = '';
    foreach ($arr as $item) {
        if($item){
            $str .= '<img src="/Uploads/'.$item.'" />';
        }
    }
    return $str;
}