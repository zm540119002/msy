<?php
function GetTimeString()
{
    date_default_timezone_set('Asia/Shanghai');
    $timestamp=time();
    $hours = date('H',$timestamp);
    $minutes = date('i',$timestamp);
    $seconds =date('s',$timestamp);
    $month = date('m',$timestamp);
    $day =  date('d',$timestamp);
    $stamp= $month.$day.$hours.$minutes.$seconds;
    return $stamp;
}

/**
 * 字符串截取，支持中文和其他编码
 * @static
 * @access public
 * @param string $str 需要转换的字符串
 * @param string $start 开始位置
 * @param string $length 截取长度
 * @param string $charset 编码格式
 * @param string $suffix 截断显示字符
 * @return string
 */
function msubstr($str, $start = 0, $length, $charset = "utf-8", $suffix = true)
{
    if (function_exists("mb_substr"))
        $slice = mb_substr($str, $start, $length, $charset);
    elseif (function_exists('iconv_substr')) {
        $slice = iconv_substr($str, $start, $length, $charset);
        if (false === $slice) {
            $slice = '';
        }
    } else {
        $re['utf-8'] = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
        $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
        $re['gbk'] = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
        $re['big5'] = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
        preg_match_all($re[$charset], $str, $match);
        $slice = join("", array_slice($match[0], $start, $length));
    }
    return $suffix ? $slice . '...' : $slice;
}

/**
 * 系统加密方法
 * @param string $data 要加密的字符串
 * @param string $key 加密密钥
 * @param int $expire 过期时间 单位 秒
 * @return string
 */
function think_encrypt($data, $key = '', $expire = 0)
{
    $key = md5(empty($key) ? C('DATA_AUTH_KEY') : $key);
    $data = base64_encode($data);
    $x = 0;
    $len = strlen($data);
    $l = strlen($key);
    $char = '';

    for ($i = 0; $i < $len; $i++) {
        if ($x == $l) $x = 0;
        $char .= substr($key, $x, 1);
        $x++;
    }

    $str = sprintf('%010d', $expire ? $expire + time() : 0);

    for ($i = 0; $i < $len; $i++) {
        $str .= chr(ord(substr($data, $i, 1)) + (ord(substr($char, $i, 1))) % 256);
    }
    return str_replace(array('+', '/', '='), array('-', '_', ''), base64_encode($str));
}

/**
 * 系统解密方法
 * @param  string $data 要解密的字符串 （必须是think_encrypt方法加密的字符串）
 * @param  string $key 加密密钥
 * @return string
 */
function think_decrypt($data, $key = '')
{
    $key = md5(empty($key) ? C('DATA_AUTH_KEY') : $key);
    $data = str_replace(array('-', '_'), array('+', '/'), $data);
    $mod4 = strlen($data) % 4;
    if ($mod4) {
        $data .= substr('====', $mod4);
    }
    $data = base64_decode($data);
    $expire = substr($data, 0, 10);
    $data = substr($data, 10);

    if ($expire > 0 && $expire < time()) {
        return '';
    }
    $x = 0;
    $len = strlen($data);
    $l = strlen($key);
    $char = $str = '';

    for ($i = 0; $i < $len; $i++) {
        if ($x == $l) $x = 0;
        $char .= substr($key, $x, 1);
        $x++;
    }

    for ($i = 0; $i < $len; $i++) {
        if (ord(substr($data, $i, 1)) < ord(substr($char, $i, 1))) {
            $str .= chr((ord(substr($data, $i, 1)) + 256) - ord(substr($char, $i, 1)));
        } else {
            $str .= chr(ord(substr($data, $i, 1)) - ord(substr($char, $i, 1)));
        }
    }
    return base64_decode($str);
}

/**
 * 数据签名认证
 * @param  array $data 被认证的数据
 * @return string       签名
 */
function data_auth_sign($data)
{
    //数据类型检测
    if (!is_array($data)) {
        $data = (array)$data;
    }
    ksort($data); //排序
    $code = http_build_query($data); //url编码并生成query字符串
    $sign = sha1($code); //生成签名
    return $sign;
}

/**
 * $this->error() 的Ajax格式
 * @param $msg
 * @param string $extend
 * @return array
 */
function errorMsg($msg, $extend = '')
{
    $return = array(
        'status' => 0,
        'info' => $msg
    );
    is_array($extend) && ($return = array_merge($return, $extend));
    return $return;
}

/**
 * $this->success() 的Ajax格式
 * @param $msg
 * @param string $extend
 * @return array
 */
function successMsg($msg, $extend = '')
{
    $return = array(
        'status' => 1,
        'info' => $msg
    );
    is_array($extend) && ($return = array_merge($return, $extend));
    return $return;
}

/**
 * Compares two strings $a and $b in length-constant time.
 * @param $a
 * @param $b
 * @return bool
 */
function slow_equals($a, $b)
{
    $diff = strlen($a) ^ strlen($b);
    for ($i = 0; $i < strlen($a) && $i < strlen($b); $i++) {
        $diff |= ord($a[$i]) ^ ord($b[$i]);
    }
    return $diff === 0;
}

/**
 * @param $mobile
 * @return bool
 */
function isMobile($mobile)
{
    return preg_match("/^1[34578]\d{9}$/", trim($mobile)) ? true : false;
}

/**
 * @param $email
 * @return bool
 */
function isEmail($email)
{
    return (strlen($email) > 6 && preg_match("/^[\w\-\.]+@[\w\-\.]+(\.\w+)+$/", $email)) ? true : false;
}

/**
 * @param $qq
 * @return bool
 */
function isQQ($qq)
{
    return preg_match('/^[1-9]\d{4,12}$/', $qq) ? true : false;
}

//验证不能为0的正数 11.1 | 0
function checkPricePlus($int)
{
    if (is_numeric($int) > 0) {
        if ($int > 0) {
            return true;
        } else {
            return false;
        }
    }
    return false;
}

//验证可以为0的正数 11.1
function checkPriceZero($int)
{
    if (is_numeric($int) > 0) {
        if ($int >= 0) {
            return true;
        } else {
            return false;
        }
    }
    return false;
}

//正整数
function positiveInteger($int)
{
    if (preg_match("/^[1-9]\d*$/", $int)) {
        return true;
    } else {
        return false;
    }
}

//非负正数
function nonNegativeInteger($int)
{
    if (preg_match("/^\d+$/", $int)) {
        return true;
    } else {
        return false;
    }
}

/**
 * 产生随机字串，可用来自动生成密码
 * 默认长度6位 数字 支持中文
 * @param string $len 长度
 * @param string $type 字串类型
 * 0--大小写字母混合 1--数字 2--大写字母 3--小写字母 4--中文汉字 其它--大小写字母混合
 * @param string $addChars 额外字符
 * @return string
 */
function create_random_str($len = 6, $type = 1, $prefixChars = "", $addChars = "")
{
    $range_code = Org\Util\String::randString($len, $type, $addChars);
    return $prefixChars ? $prefixChars . $range_code : $range_code;
}

/*
 *读取URL中传来的参数
 *@param string $variablename 参数的名称
 *@return string 参数值
 */
function get_url_param($variableName)
{
    return urldecode(I($variableName));
}

/*
 * 获取无参数URL
 */
function get_current_page_url()
{
    $pageURL = 'http';
    if ($_SERVER["HTTPS"] == "on") {
        $pageURL .= "s";
    }
    $pageURL .= "://";
    $this_page = $_SERVER["REQUEST_URI"];
    // 只取 ? 前面的内容
    if (strpos($this_page, "?") !== false)
        $this_page = reset(explode("?", $this_page));
    if ($_SERVER["SERVER_PORT"] != "80") {
        $pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $this_page;
    } else {
        $pageURL .= $_SERVER["SERVER_NAME"] . $this_page;
    }
    return $pageURL;
}

/**
 * 循环创建目录
 */
function mk_dir($dir, $mode = 0755)
{
    if (is_dir($dir) || @mkdir($dir, $mode)) return true;
    if (!mk_dir(dirname($dir), $mode)) return false;
    return @mkdir($dir, $mode);
}

/**
 * 计算 UTF-8 字符串长度（忽略字节的方案）
 *
 * @param string $str
 * @return int
 */
function strlen_utf8($str)
{
    $i = 0;
    $count = 0;
    $len = strlen($str);
    while ($i < $len) {
        $chr = ord($str[$i]);
        $count++;
        $i++;
        if ($i >= $len) {
            break;
        }
        if ($chr & 0x80) {
            $chr <<= 1;
            while ($chr & 0x80) {
                $i++;
                $chr <<= 1;
            }
        }
    }
    return $count;
}

/**
 * 验证字符长度是否在某个区间，
 * $str : 表单字段接收的内容，
 * $min:最小长度，
 * max:最大长度，
 */
function checklength($str, $min = 6, $max = 10)
{
    preg_match_all("/./u", $str, $matches);
    $len = count($matches[0]);
    if ($len < $min || $len > $max) {
        return false;
    } else {
        return true;
    }
}

/**
 * 上传文件（单个）
 * @param $conf
 * @param bool $dbTemp
 * @return array
 */
function think_upload($conf)
{
    if (!array_key_exists('savePath', $conf)) {
        throw_exception('未设置savePath key值');
    }
    $config = array(
        'maxSize' => 2 * 1024 * 1024,
        'rootPath' => C('UPLOAD_PATH'),
        'autoSub' => true,
        'replace' => true,
        'exts' => array('jpg', 'gif', 'png', 'jpeg'),
        'subName' => '',
    );
    $upload = new \Think\Upload(array_merge($config, $conf));
    $info = $upload->upload();
    if (!$info) {
        return array(false, $upload->getError());
    }
    return array(true, $info[0]);
}

/*
 *重定向
 */
function immediatelyJump($url){
    header("location: ".$url);
    exit;
}

/**多维数组排序
 * @param $array
 * @param $field
 * @param bool $desc
 */
function sortArrByField(&$array, $field, $desc = false){
    $fieldArr = array();
    foreach ($array as $k => $v) {
        $fieldArr[$k] = $v[$field];
    }
    $sort = $desc == false ? SORT_ASC : SORT_DESC;
    array_multisort($fieldArr, $sort, $array);
}

/**
 * 将xml转换为数组
 * @param string $xml:xml文件或字符串
 * @return array
 */
function xmlToArray($xml){
    //考虑到xml文档中可能会包含<![CDATA[]]>标签，第三个参数设置为LIBXML_NOCDATA
    if (file_exists($xml)) {
        libxml_disable_entity_loader(false);
        $xml_string = simplexml_load_file($xml,'SimpleXMLElement', LIBXML_NOCDATA);
    }else{
        libxml_disable_entity_loader(true);
        $xml_string = simplexml_load_string($xml,'SimpleXMLElement', LIBXML_NOCDATA);
    }
    $result = json_decode(json_encode($xml_string),true);
    return $result;
}

/**验证密码格式
 * @param $passwd
 * @return bool
 */
function checkPwd($passwd){
    return true;
}

/**
 * 将数组转换为xml
 * @param array $arr:数组
 * @param object $dom:Document对象，默认null即可
 * @param object $node:节点对象，默认null即可
 * @param string $root:根节点名称
 * @param string $cdata:是否加入CDATA标签，默认为false
 * @return string
 */
function arrayToXml($arr,$dom=null,$node=null,$root='xml',$cdata=false){
    if (!$dom){
        $dom = new DOMDocument('1.0','utf-8');
    }
    if(!$node){
        $node = $dom->createElement($root);
        $dom->appendChild($node);
    }
    foreach ($arr as $key=>$value){
        $child_node = $dom->createElement(is_string($key) ? $key : 'node');
        $node->appendChild($child_node);
        if (!is_array($value)){
            if (!$cdata) {
                $data = $dom->createTextNode($value);
            }else{
                $data = $dom->createCDATASection($value);
            }
            $child_node->appendChild($data);
        }else {
            arrayToXml($value,$dom,$child_node,$root,$cdata);
        }
    }
    return $dom->saveXML();
}




///**
// * 生成签名
// * @return 签名，本函数不覆盖sign成员变量
// */
//require_once(dirname(dirname(__FILE__)) . '/Component/WxpayAPI/lib/WxPay.Api.php');
// function makeSign($data){
//    //获取微信支付秘钥
//    $key = C('WX_CONFIG')['KEY'];
//    // 去空
//    $data=array_filter($data);
//    //签名步骤一：按字典序排序参数
//    ksort($data);
//    $string_a=http_build_query($data);
//    $string_a=urldecode($string_a);
//    //签名步骤二：在string后加入KEY
//    //$config=$this->config;
//    $string_sign_temp=$string_a."&key=".$key;
//    //签名步骤三：MD5加密
//    $sign = md5($string_sign_temp);
//    // 签名步骤四：所有字符转为大写
//    $result=strtoupper($sign);
//    return $result;
//}

/**
 * 是否为微信浏览器
 * @return bool
 */
function isWxBrowser(){
    if ( strpos($_SERVER['HTTP_USER_AGENT'],'MicroMessenger') !== false ){
        return true;
    }
    return false;
}

/**生成订单编号（19位纯数字）
 * @return string
 */
function generateSN($len = 18){
    return date('YmdHis',time()) . create_random_str($len);
}

/**验证账号是否存在
 * @param $name
 * @return bool
 */
function accountIsExist($name){
    return M('user')->where(array('name'=>$name,))->count()?true:false;
}

/**判断是否预留手机号码
 * @param $mobile
 * @param $name
 * @return bool
 */
function isReservedMobilePhone($mobile,$name){
    return (M('user')->where(array('name'=>trim($name),))->getField('mobile_phone') == trim($mobile)) ? true : false;
}


/**
 * 过滤数组元素前后空格 (支持多维数组)
 * @param $array 要过滤的数组
 * @return array|string
 */
function trim_array_element($array){
    if(!is_array($array))
        return trim($array);
    return array_map('trim_array_element',$array);
}