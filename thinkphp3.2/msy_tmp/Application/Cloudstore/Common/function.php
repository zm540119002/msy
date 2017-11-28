<?php
/**
 * Created by PhpStorm.
 * User: zm
 * Date: 2017/2/28
 * Time: 14:17
 */
/**获取后N天的日期和星期
 * @param int $n    默认后6天
 * @return array
 */
function getNextNDate($n=6){
    $arr = array();
    $nowTimeStamp = time();

    for($i=1;$i<=intval($n);$i++){
        $nTimeStamp = strtotime('+'.$i.' day',$nowTimeStamp);
        $arr[$i]['date'] = date('m 月 d 日 ',$nTimeStamp);
        $arr[$i]['week'] = formatWeekFromNumber(date('w',$nTimeStamp)) ;
    }

    return $arr;
}

function formatWeekFromNumber($w){
    $arr = array(
        '0'=>'星期日',
        '1'=>'星期一',
        '2'=>'星期二',
        '3'=>'星期三',
        '4'=>'星期四',
        '5'=>'星期五',
        '6'=>'星期六',
    );
    return $arr[$w];
}
