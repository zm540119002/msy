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