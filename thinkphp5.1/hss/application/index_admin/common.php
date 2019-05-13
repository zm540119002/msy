<?php
function onOffLine($number) {
    $arr = config('ON_OFF_LINE');
    return $arr[intval($number)];
}

function formatImg($str){
    $arr = explode(',',$str);
    $str = '';
    foreach ($arr as $item) {
        if($item){
            $str .= '<img src="/uploads/'.$item.'" />';
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
            if($value['goods_id'] == $val['goods_id']){
                $value['num'] += $val['num'];
            }
        }
    }
    foreach ($new as $item){
        $find = false;
        foreach ($old as $val){
            if($item['goods_id'] == $val['goods_id']){
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
            if($value['id'] == $val['goods_id']){
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


//获取下上架类型
function getShelStatus($num){
    return $num?config('custom.shelf_status')[$num]:'';
}

//获取下上架类型
function getAuthStatus($num){
    return $num?config('custom.auth_status')[$num]:'';
}

// 上传用
/**
 * 1、文件重命名
 * @param $data array
 * @param $arr array 要处理的字段
 * @param bool $multiple
 */

function process_upload_files(&$data,$arr,$multiple=true){

    foreach($arr as $k => $v){

        $file = $data[$v];

        if($multiple){
            if($file!=null){
                $detailArr = explode(',',$file);
                $tempArr = array();
                foreach ($detailArr as $item) {
                    if($item){
                        $tempArr[] = moveImgFromTemp(config('upload_dir.project'),$item);
                    }
                }
                $data[$v] = implode(',',$tempArr);
            }

        }else{
            $data[$v] = moveImgFromTemp(config('upload_dir.project'),$file);
        }

    }
}

/**
 * @param $data array
 * @param $arr array 要处理的字段
 */
function htmlspecialchars_addslashes(&$data,$arr){
    foreach($arr as $k => $v){

        $str = $data[$v];

        if($str!=null){
            $data[$v] = htmlspecialchars(addslashes($str));
        }
    }
}

/**
 * 替换分割符
 */
function replace_splitter(&$data,$arr){
    foreach($arr as $k => $v){

        $str = $data[$v];
        if($str!=null){
            $data[$v] = str_replace('，',',',$str);
        }
    }


}

