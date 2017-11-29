<?php
require_once(APP_PATH . '/Common/Common/ext_function.php');
require_once(WEB_PATH . '../web/all/Common/page.php');
require_once(WEB_PATH . '../web/all/Common/function.php');

function couponsType($num){
    switch ($num){
        case 1:
            return '商城';
        case 2:
            return '预约';
        default:
            return '未知';
    }
}
