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
        'PUBLIC_IMG' => Request::domain() .'/static/common/img',
        'PUBLIC_JS' => Request::domain() .'/static/common/js',
        'PUBLIC_CSS' => Request::domain() .'/static/common/css',

        'PUBLIC_IMG_INDEX' => Request::domain() .'/static/index/img',
        'PUBLIC_JS_INDEX' => Request::domain() .'/static/index/js',
        'PUBLIC_CSS_INDEX' => Request::domain() .'/static/index/css',

        'PUBLIC_IMG_ADMIN' => Request::domain() .'/static/admin/img',
        'PUBLIC_JS_ADMIN' => Request::domain() .'/static/admin/js',
        'PUBLIC_CSS_ADMIN' => Request::domain() .'/static/admin/css',
        'PUBLIC_HUI_ADMIN' => Request::domain() .'/static/hadmin',

        'PUBLIC_IMG_STORE' => Request::domain() .'/static/store/img',
        'PUBLIC_JS_STORE' => Request::domain() .'/static/store/js',
        'PUBLIC_CSS_STORE' => Request::domain() .'/static/store/css',

        'PUBLIC_IMG_STORE_ADMIN' => Request::domain() .'/static/store_admin/img',
        'PUBLIC_JS_STORE_ADMIN' => Request::domain() .'/static/store_admin/js',
        'PUBLIC_CSS_STORE_ADMIN' => Request::domain() .'/static/store_admin/css',

        'PUBLIC_IMG_VENDOR' => Request::domain() .'/static/vendor/img',
        'PUBLIC_JS_VENDOR' => Request::domain() .'/static/vendor/js',
        'PUBLIC_CSS_VENDOR' => Request::domain() .'/static/vendor/css',

        'PUBLIC_IMG_VENDOR_ADMIN' => Request::domain() .'/static/vendor_admin/img',
        'PUBLIC_JS_VENDOR_ADMIN' => Request::domain() .'/static/vendor_admin/js',
        'PUBLIC_CSS_VENDOR_ADMIN' => Request::domain() .'/static/vendor_admin/css',


    ],
];
