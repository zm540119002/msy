<?php
return array(
    //'配置项'=>'配置值'
    'URL_CASE_INSENSITIVE' =>false,//URL区分大小写(true)
    'DEFAULT_EXPIRE' => 24 * 60 * 60, //默认有效时间
    'DEFAULT_PAGE_SIZE' => 5,//默认页数
    'URL_MODEL' => 1,

    //默认链接
    'DB_TYPE'   => 'mysql', // 数据库类型
    'DB_HOST'   => '127.0.0.1', // 服务器地址
    'DB_NAME'   => 'msy', // 数据库名
    'DB_USER'   => 'root', // 用户名
    'DB_PWD'    => 'msy', // 密码
    'DB_PORT'   =>  3306, // 端口
    'DB_PREFIX' => '',  //数据库表前缀

    //Cookie
    'SESSION_PREFIX' => 'Common_',
    'COOKIE_PATH' => '/',

    //Cache
    'DATA_CACHE_TIME' => 3600,
    'DATA_CACHE_SUBDIR' => true,
    'DATA_PATH_LEVEL' => 1,

    //调试
    'SHOW_PAGE_TRACE' => false,
    'LOG_RECORD' => true, // 开启日志记录
    'LOG_LEVEL'  =>'EMERG,ALERT,CRIT,ERR', // 只记录EMERG ALERT CRIT ERR 错误

    //错误跳转模板文件路径
    'TMPL_ACTION_SUCCESS' => 'Public:success',// 默认成功跳转对应的模板文件
//    'TMPL_ACTION_ERROR' => 'Public:error',// 默认错误跳转对应的模板文件

    //额外配置文件
    'LOAD_EXT_CONFIG' => 'ext_conf,db,wx_conf,array_conf',
);
