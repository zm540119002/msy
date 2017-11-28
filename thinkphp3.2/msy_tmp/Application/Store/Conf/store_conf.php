<?php
return array(
    //上传目录
    'UPLOAD_PATH' => 'Uploads/',
    'CACHE_IMAGE_PATH' => 'temp/',//图片上传临时存放区
    'ORGANIZATION_FIGURE_PATH' =>'organization/figure/',//机构形象图片
    'ORGANIZATION_VERIFY_PATH' =>'organization/verify/',//机构认证图片
    'SHOP_LOGO' =>'Uploads/shop/logo/',
    'PROJECT_PATH' =>'shop/project/',
    'SCALE' => @include_once(APP_PATH . 'Common/Conf/scale_conf.php'),
    'POSITION' => @include_once(APP_PATH . 'Common/Conf/position_conf.php'),
);