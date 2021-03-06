<?php
// 异常错误报错级别,
error_reporting(E_ERROR | E_PARSE );
require __DIR__ . '/../../common/function/common.php';

/**循环判断键值是否存在
 * @return bool
 */
function multi_array_key_exists( $needle, $haystack ) {
    foreach ( $haystack as $key => $value ) :
        if ( $needle == $key )
            return true;
        if ( is_array( $value ) ) :
            if ( multi_array_key_exists( $needle, $value ) == true )
                return true;
            else
                continue;
        endif;
    endforeach;
    return false;
}
//获取店铺类型
function getStoreType($num){
    return $num?config('custom.store_type')[$num]:'';
}
//获取店铺经营类型
function getRunType($num){
    return $num?config('custom.run_type')[$num]:'';
}
//获取店铺合作类型
function getOperationalModel($num){
    return $num?config('custom.operational_model')[$num]:'';
}
//获取岗位中文
function getPostCn($num){
    $post = config('permission.post');
    $res = '';
    foreach ($post as $value){
        if($num == $value['id']){
            $res = $value['name'];
        }
    }
    return $res;
}
//获取职务中文
function getDutyCn($num){
    $duty = config('permission.duty');
    $res = '';
    foreach ($duty as $value){
        if($num == $value['id']){
            $res = $value['name'];
        }
    }
    return $res;
}

//获取店铺合作类型
function getUnit($num){
    return $num?config('custom.unit')[$num]:'';
}