<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// [ 应用入口文件 ]
namespace think;
//header('Access-Control-Allow-Origin: *');
//header("Access-Control-Allow-Headers: token,Origin, X-Requested-With, Content-Type, Accept");
//header('Access-Control-Allow-Methods: POST,GET');
print_r( $_SERVER['REQUEST_METHOD'] );
print_r( $_SERVER['REMOTE_ADDR'] );
exit;
// 加载基础文件
require __DIR__ . '/../../framework/base.php';

// 支持事先使用静态方法设置Request对象和Config对象

// 执行应用并响应
Container::get('app')->run()->send();