<?php
return array(
	//'配置项'=>'配置值'

    //设置为info模式
    'URL_MODEL'=>1,
    //引入连接数据库设置
    'LOAD_EXT_CONFIG' => 'db,store_conf',
    //打开页面追踪调试
    'SHOW_PAGE_TRACE'=>true,
    //'MD5_PRE' => 'sing_cms',
    'HTML_FILE_SUFFIX' => '.html',
	
	
	
	 /* 模板相关配置 */
    'TMPL_PARSE_STRING' => array(
        '__STATIC__' => __ROOT__ . '/Public/static',
        '__ADDONS__' => __ROOT__ . '/Public/' . MODULE_NAME . '/Addons',
        '__IMG__'    => __ROOT__ . '/Public/' . MODULE_NAME . '/img',
        '__CSS__'    => __ROOT__ . '/Public/' . MODULE_NAME . '/css',
        '__JS__'     => __ROOT__ . '/Public/' . MODULE_NAME . '/js',
    ),
);

