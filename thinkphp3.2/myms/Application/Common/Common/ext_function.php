<?php

/** 购物车中商品个数添加到商品列表中（id相同num追加到新的数组中）
 * @param $old
 * @param $new
 */
function GoodsNumMergeById($old,$new){
    foreach ($new as &$value){
        foreach ($old as $val){
            if($value['id'] == $val['foreign_id']){
                $value['num'] = $val['num'];
                $value['foreign_id'] = $val['foreign_id'];
                $value['type'] = $val['type'];
                break;
            }
        }
    }
    return $new;
}
/*开启底部购物车配置项
 */
function unlockingFooterCartConfig($arr){
    $footerCartConfig = C('FOOTER_CART_MENU');
    $tempArr = array();
    foreach ($arr as $val) {
        $tempArr[] = $footerCartConfig[$val];
    }
    return $tempArr;
}