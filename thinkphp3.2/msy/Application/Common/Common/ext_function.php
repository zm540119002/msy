<?php

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
                break;
            }
        }
    }
    return $new;
}

/**获取单位值
 * @param $num
 * @return string
 */
function getUnitCN($num){
    $modelUnit = D('Unit');
    $unitList = $modelUnit->selectUnit();
    foreach ($unitList as $unit) {
        if($num == $unit['key']){
            return $unit['value'];
        }
    }
    return '';
}