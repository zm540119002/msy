<?php


/**公用的方法
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


/**@param $status
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
        //$this->ajaxReturn(successMsg('验证码已发送至手机:'.$mobilePhone . '，请注意查收。'));
        return show(1,"验证码已发送至手机:'.$mobilePhone. ',请注意查收");
    }catch (SoapFault $fault){
        //$this->ajaxReturn(errorMsg("验证码发送失败,请稍候再试。。。"));
        return show(0,"验证码发送失败,请稍候再试。。。");
    }
}


/**@param $arr
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


/**返回任务单签约状态-中文
 * @param $number
 * @return mixed
 */
function onOffLine($number) {
    $arr = C('ON_OFF_LINE');
    return $arr[intval($number)];
}

function formatImg($str){
    $arr = explode(',',$str);
    $str = '';
    foreach ($arr as $item) {
        if($item){
            $str .= '<img src="/Uploads/'.$item.'" />';
        }
    }
    return $str;
}