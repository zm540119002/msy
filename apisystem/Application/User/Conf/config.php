<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------
// +----------------------------------------------------------------------
// | ApiSystem接口文档管理系统 让沟通更简单
// | Copyright (c) 2015 http://www.apisystem.cn
// | Author: Texren  QQ: 174463651
// |         Smith77 QQ: 3246932472
// | 交流QQ群 577693968 交流QQ群2 460098419
// +----------------------------------------------------------------------


/**
 * UCenter客户端配置文件
 * 注意：该配置文件请使用常量方式定义
 */

define('UC_APP_ID', 1); //应用ID
define('UC_API_TYPE', 'Model'); //可选值 Model / Service
define('UC_AUTH_KEY', 'f_a^J~Ru{Y)k1%LMP}KT`!5&X>O4rCx,#/n<U;AV'); //加密KEY
define('UC_DB_DSN', C('DB_TYPE') . '://' . C('DB_USER') . ':' . C('DB_PWD') . '@' . C('DB_HOST') . ':' . C('DB_PORT') . '/' . C('DB_NAME'));
define('UC_TABLE_PREFIX', C('DB_PREFIX'));

//可以不用配置下面信息
//define('UC_DB_DSN', 'mysqli://root:root@127.0.0.1:3306/apisys'); // 数据库连接，使用Model方式调用API必须配置此项
//define('UC_TABLE_PREFIX', 'apisys_'); // 数据表前缀，使用Model方式调用API必须配置此项

