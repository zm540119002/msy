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

        'INDEX_IMG' => Request::domain() .'/static/index/img',
        'INDEX_JS' => Request::domain() .'/static/index/js',
        'INDEX_CSS' => Request::domain() .'/static/index/css',

        'STORE_IMG' => Request::domain() .'/static/store/img',
        'STORE_JS' => Request::domain() .'/static/store/js',
        'STORE_CSS' => Request::domain() .'/static/store/css',

        'STORE_ADMIN_IMG' => Request::domain() .'/static/store_admin/img',
        'STORE_ADMIN_JS' => Request::domain() .'/static/store_admin/js',
        'STORE_ADMIN_CSS' => Request::domain() .'/static/store_admin/css',

        'VENDOR_IMG' => Request::domain() .'/static/vendor/img',
        'VENDOR_JS' => Request::domain() .'/static/vendor/js',
        'VENDOR_CSS' => Request::domain() .'/static/vendor/css',

        'VENDOR_ADMIN_IMG' => Request::domain() .'/static/vendor_admin/img',
        'VENDOR_ADMIN_JS' => Request::domain() .'/static/vendor_admin/js',
        'VENDOR_ADMIN_CSS' => Request::domain() .'/static/vendor_admin/css',

        'PUBLIC_HUI_ADMIN' => Request::domain() .'/static/hadmin',

    ],
];
