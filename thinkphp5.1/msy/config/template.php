<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// +----------------------------------------------------------------------
// | 模板设置
// +----------------------------------------------------------------------
//注意：模块不能覆盖
return [
    // 模板引擎类型 支持 php think 支持扩展
    'type'         => 'Think',
    // 模板路径
    'view_path'    => '',
    // 模板后缀
    'view_suffix'  => 'html',
    // 模板文件名分隔符
    'view_depr'    => DIRECTORY_SEPARATOR,
    // 模板引擎普通标签开始标记
    'tpl_begin'    => '{',
    // 模板引擎普通标签结束标记
    'tpl_end'      => '}',
    'taglib_begin' => '{',
    // 标签库标签开始标记
    // 标签库标签结束标记
    'taglib_end'   => '}',
    'tpl_replace_string' => [
        'public_img' => Request::domain() .'/static/common/img',
        'public_js' => Request::domain() .'/static/common/js',
        'public_css' => Request::domain() .'/static/common/css',

        'index_img' => Request::domain() .'/static/index/img',
        'index_js' => Request::domain() .'/static/index/js',
        'index_css' => Request::domain() .'/static/index/css',

        'store_img' => Request::domain() .'/static/store/img',
        'store_js' => Request::domain() .'/static/store/js',
        'store_css' => Request::domain() .'/static/store/css',

        'store_admin_img' => Request::domain() .'/static/store_admin/img',
        'store_admin_js' => Request::domain() .'/static/store_admin/js',
        'store_admin_css' => Request::domain() .'/static/store_admin/css',

        'vendor_img' => Request::domain() .'/static/vendor/img',
        'vendor_js' => Request::domain() .'/static/vendor/js',
        'vendor_css' => Request::domain() .'/static/vendor/css',

        'vendor_admin_img' => Request::domain() .'/static/vendor_admin/img',
        'vendor_admin_js' => Request::domain() .'/static/vendor_admin/js',
        'vendor_admin_css' => Request::domain() .'/static/vendor_admin/css',

        'public_hui_admin' => Request::domain() .'/static/hadmin',

    ],
];
