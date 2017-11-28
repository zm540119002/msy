<?php
//php获取今日开始时间戳和结束时间戳
function beginToday(){
    return mktime(0,0,0,date('m'),date('d'),date('Y'));
}

function endToday(){
    return mktime(0,0,0,date('m'),date('d')+1,date('Y'))-1;
}
