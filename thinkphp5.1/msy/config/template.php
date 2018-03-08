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
        'MODULE' => Request::module(),
        'CONTROLLER' => Request::controller(),
        'ACTION' => Request::action(),
        'PUBLIC_IMG' => Request::root() .'/public/static/img/',
        'PUBLIC_JS' => Request::root() .'/public/static/js/',
        'PUBLIC_CSS' => Request::root() .'/public/static/js/css/',
    ],
];
