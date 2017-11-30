<?php
return array(
    //上传路径
    'UPLOAD_PATH' => './Uploads/',

    'SCALE' => @include_once(APP_PATH . 'Common/Conf/scale_conf.php'),
    'POSITION' => @include_once(APP_PATH . 'Common/Conf/position_conf.php'),
);