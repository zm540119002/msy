<?php

// scene系列
/**
 * 处理场景信息
 * @param $info array 场景信息
 */
function scene_handle(&$info){
    $info['tag']      = $info['tag']      ? explode(',',(string)$info['tag']) : '';
    $info['main_img'] = $info['main_img'] ? explode(',',(string)$info['main_img']) : '';
    $info['intro']    = $info['intro']    ? htmlspecialchars_decode($info['intro']) : '';
};


// project系列
/**
 * 处理项目信息
 * @param $info array 场景信息
 */
function project_handle(&$info){
    $info['tag']        = $info['tag']      ? explode(',',(string)$info['tag']) : '';
    $info['main_img']   = $info['main_img'] ? explode(',',(string)$info['main_img']) : '';
    $info['detail_img'] = $info['detail_img'] ? explode(',',(string)$info['detail_img']) : '';
    $info['process_img']= $info['process_img'] ? explode(',',(string)$info['process_img']) : '';
    $info['intro']      = $info['intro']    ? htmlspecialchars_decode($info['intro']) : '';
    $info['description']= $info['description']    ? htmlspecialchars_decode($info['description']) : '';
    $info['remarks']    = $info['remarks']    ? htmlspecialchars_decode($info['remarks']) : '';
    $info['process']    = $info['process']    ? json_decode($info['process'],true) : [];
};

// sort系列
/**
 * 处理品类信息
 * @param $info array 场景信息
 */
function sort_handle(&$info){
    $info['tag']        = $info['tag']      ? explode(',',(string)$info['tag']) : '';
    $info['main_img']   = $info['main_img'] ? explode(',',(string)$info['main_img']) : '';
    $info['detail_img'] = $info['detail_img'] ? explode(',',(string)$info['detail_img']) : '';
    $info['process_img']= $info['process_img'] ? explode(',',(string)$info['process_img']) : '';
    $info['intro']      = $info['intro']    ? htmlspecialchars_decode($info['intro']) : '';
};

// promotion系列
/**
 * 处理项目信息
 * @param $info array 场景信息
 */
function promotion_handle(&$info){
    $info['tag']      = $info['tag']      ? explode(',',(string)$info['tag']) : '';
    $info['main_img'] = $info['main_img'] ? explode(',',(string)$info['main_img']) : '';
    $info['intro']    = $info['intro']    ? htmlspecialchars_decode($info['intro']) : '';
};

/**
 * 底部购物车栏
 */
function foot_cart_menu(){
    $unlockingFooterCart = unlockingFooterCartConfigTest([0,2,1,3]);
    array_push($unlockingFooterCart['menu'][0]['class'],'group_btn30');
    array_push($unlockingFooterCart['menu'][1]['class'],'group_btn20');
    array_push($unlockingFooterCart['menu'][2]['class'],'group_btn25');
    array_push($unlockingFooterCart['menu'][3]['class'],'group_btn25');
    think\View::share('unlockingFooterCart',json_encode($unlockingFooterCart));

    controller('index')->getCartTotalNum();

    //app\index\controller\Cart::getCartTotalNum();
}
