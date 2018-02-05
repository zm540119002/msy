<?php

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

/** 商品二维数组合并（id相同num相加）
 * @param $old
 * @param $new
 */
function goodsMergeById($old,$new){
    if(empty($old))
        return $new;
    if(empty($new))
        return $old;
    foreach ($old as &$value){
        foreach ($new as $val){
            if($value['foreign_id'] == $val['foreign_id']){
                $value['num'] += $val['num'];
            }
        }
    }
    foreach ($new as $item){
        $find = false;
        foreach ($old as $val){
            if($item['foreign_id'] == $val['foreign_id']){
                $find = true;
                break;
            }
        }
        if(!$find){
            $old[] = $item;
        }
    }
    return $old;
}

/** 购物车中商品个数添加到商品列表中（id相同num追加到新的数组中）
 * @param $old
 * @param $new
 */
function GoodsNumMergeById($old,$new){
    foreach ($new as &$value){
        foreach ($old as $val){
            if($value['id'] == $val['foreign_id']){
                $value['num'] = $val['num'];
                $value['goods_type'] = $val['goods_type'];
                $value['foreign_id'] = $val['foreign_id'];
                break;
            }
        }
    }
    return $new;
}