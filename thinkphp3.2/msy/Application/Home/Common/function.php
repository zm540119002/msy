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

/**手机验证码验证
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

/**发送手机验证码
 * @param $mobilePhone 手机号码
 */
   function sent_verify($mobilePhone){
    $url = "http://sms3.mobset.com:8080/Cloud?wsdl";
    $client = new \SoapClient($url);
    $client->soap_defencoding = 'utf-8';
    $client->decode_utf8 = false;
    $addNum="";
    $timer="";
    $lCorpID = "301289";
    $strLoginName = "Admin";
    $strPasswd = "sun19760924_++";
    $captcha = create_random_str(6);
    $smsContent = '验证码：'.$captcha.' 该验证码10分钟内有效';
    $longSms=0;
    $strTimeStamp=GetTimeString();
    $strInput=$lCorpID.$strPasswd.$strTimeStamp;
    $strMd5=md5($strInput);
    $group=$client-> ArrayOfMobileList[1];
    $group[0] =$client->MobileListGroup;
    $group[0]->Mobile = $mobilePhone;
    $param = array(
        'CorpID'=>$lCorpID,
        'LoginName'=>$strLoginName,
        'Password'=>$strMd5,
        'TimeStamp'=>$strTimeStamp,
        'AddNum'=>$addNum,
        'Timer'=>$timer,
        'LongSms'=>$longSms,
        'MobileList'=>$group,
        'Content'=>$smsContent
    );
    try {
        $client->Sms_Send($param);
        $type = I('post.type','','string') ;
        session('captcha_'.($type ? $type : 'register').'_'.$mobilePhone,$captcha,60*10);
        return show(1,"验证码已发送至手机:'.$mobilePhone. ',请注意查收");
    }catch (SoapFault $fault){
        return show(0,"验证码发送失败,请稍候再试。。。");
    }
}

/**上传图片
 * @param $base64_image_content  图片的base64源
 * @param string $path   保存路径
 * @return string  返回图片的位置
 */
function upload($base64_image_content,$uploaddir=""){
    header('Content-type:text/html;charset=utf-8');

    $position = $_POST['name'];//确定那个input表单提交的
    //var_dump($_POST);exit;
    $position ? $position : "";
    $uploaddir ? $uploaddir : "";

    //匹配出图片的格式
    if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64_image_content, $result)){
        $type = $result[2];
        $typeArr = array("jpeg","gif","png");
        if (!in_array($type, $typeArr)) {
            show(0,"请上传jpg,png或gif类型的图片！");
            exit;
        }

        if(!file_exists($uploaddir))
        {
            //检查是否有该文件夹，如果没有就创建，并给予最高权限
            if(!mkdir($uploaddir, 0700,true)){
                show(0,'创建目录失败');
            }
        }
        $img = $uploaddir.time()."{$position}.{$type}";
        if (file_put_contents($img, base64_decode(str_replace($result[1], '', $base64_image_content)))){
            return time()."{$position}.{$type}";
        }else{
            show(0,"保存图片失败！");
        }
    }
}

/**将数据库中查出的列表以指定的 id 作为数组的键名
 * @param $arr
 * @param $key_name
 * @return array
 */
function convert_arr_key($arr, $key_name)
{
    $arr2 = array();
    foreach($arr as $key => $val){
        $arr2[$val[$key_name]] = $val;
    }
    return $arr2;
}

/**判定数组中包含数字
 * @param $array
 * @return bool
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