<?php
return array(
    //'配置项'=>'配置值'
    'URL_CASE_INSENSITIVE' =>false,//URL区分大小写(true)
    'DEFAULT_EXPIRE' => 24 * 60 * 60, //默认有效时间
    'URL_MODEL' => 1,

    //数据库
    'DB_TYPE' => 'mysql',
    'DB_HOST' => '127.0.0.1',
    'DB_NAME' => 'msy',
    'DB_USER' => 'root',
    'DB_PWD' => 'msy',
    'DB_PORT' => '3306',
    'DB_PREFIX' => '',

    //错误跳转模板文件路径
    'TMPL_ACTION_SUCCESS' => 'Public:success',// 默认成功跳转对应的模板文件
//    'TMPL_ACTION_ERROR' => 'Public:error',// 默认错误跳转对应的模板文件

    //Cookie
    'SESSION_PREFIX' => 'Common_',
    'COOKIE_PREFIX' => 'Common_',

    //Cache
    'DATA_CACHE_TIME' => 3600,
    'DATA_CACHE_SUBDIR' => true,
    'DATA_PATH_LEVEL' => 1,

    //上传路径
    'UPLOAD_PATH' => './Uploads/',
    'UPLOAD_IMG_TEMP' =>'temp/',

    //调试
    'SHOW_PAGE_TRACE' => false,
    'LOG_RECORD' => true, // 开启日志记录
    'LOG_LEVEL'  =>'EMERG,ALERT,CRIT,ERR', // 只记录EMERG ALERT CRIT ERR 错误

    //额外配置文件
    'LOAD_EXT_CONFIG' => 'ext_conf',
);
