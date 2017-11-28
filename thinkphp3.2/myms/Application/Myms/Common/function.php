<?php

/**
 * 公用的方法
 */
/**
 * @param $status
 * @param $message
 * @param array $data
 */
function  show($status, $message,$data=array()) {
    $reuslt = array(
        'status' => $status,
        'message' => $message,
        'data' => $data,
    );
    exit(json_encode($reuslt));

}


/**
 * @param $status
 * @param $message
 * @param array $data
 */
function  showInfo($status, $message,$data=array()) {
    $reuslt = array(
        'status' => $status,
        'message' => $message,
        'data' => $data,
    );
    exit($reuslt);

}


/**
 * 手机验证码验证
 * @param $vfyCode  $_SESSION['vfyCode']
 * @param string $post $_POST['vfyCode']
 * @return bool
 */
   function verifyCheck($post=""){
       $vfyCode=session('captcha_'. 'register'.'_'.$_POST['phone']);
       $ver = trim($post);
       if ( $ver == $vfyCode) {
           return true;
       } else {
           return false;
       }

   }




/**
 * @param $arr
 * @param $key_name
 * @return array
 * 将数据库中查出的列表以指定的 id 作为数组的键名
 */
function convert_arr_key($arr, $key_name)
{
    $arr2 = array();
    foreach($arr as $key => $val){
        $arr2[$val[$key_name]] = $val;
    }
    return $arr2;
}

/**
 * @param $array
 * @return bool
 * 判定数组中包含数字
 */
function arrayHasOnlyInts($array)
{
    foreach ($array as $value)
    {
        if (!is_int($value)) // there are several ways to do this
        {
            return false;
        }
    }
    return true;
}


