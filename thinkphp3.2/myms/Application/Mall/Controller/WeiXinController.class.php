<?php
namespace Mall\Controller;
use  web\all\Controller\BaseController;
use web\all\Component\WxpayAPI\Jssdk;
use web\all\Controller\CommonController;

define("TOKEN", "meishangyun");
class WeiXinController extends CommonController {
    public function checkWxUser(){
        $url = $_GET['url'];
        $wechat= new Jssdk(C('WX_CONFIG')['APPID'], C('WX_CONFIG')['APPSECRET']);
        $wechat -> getOauthRedirect($url,"wxbase");
    }

    //启用并设置服务器配置后，用户发给公众号的消息以及开发者需要的事件推送，将被微信转发到该URL中
    public function valid()
    {
        $echoStr = $_GET["echostr"];
        if($this->checkSignature()){
            echo $echoStr;
            exit;
        }
    }

    //验证签名
    private function checkSignature()
    {
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];

        $token = TOKEN;
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr);
        $tmpStr = implode( $tmpArr );
        $tmpStr = sha1( $tmpStr );

        if( $tmpStr == $signature ){
            return true;
        }else{
            return false;
        }
    }

    //创建自定义菜单
    public function createMenuRaw(){
        $menu = '{
        "button":[
        {
        "type":"view",
        "name":"美尚云",
        "url":"http://msy.meishangyun.com"

        },
        {
        "type":"view",
        "name":"美妍美社",
        "url":"http://myms.meishangyun.com"
        }]
        }';
        $this-> create_menu_raw($menu);
    }

    //响应
    public function responseMsg()
    {
        $postStr = file_get_contents('php://input'); //return  fasle;
        if (!empty($postStr)){
            $this->logger("R ".$postStr);
            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $RX_TYPE = trim($postObj->MsgType);

            if (($postObj->MsgType == "event") && ($postObj->Event == "subscribe" || $postObj->Event == "unsubscribe" || $postObj->Event == "TEMPLATESENDJOBFINISH")){
                //过滤关注和取消关注事件
            }else{
                //更新互动记录
            }

            //消息类型分离
            switch ($RX_TYPE)
            {
                case "event":
                    $result = $this->receiveEvent($postObj);
                    break;
                case "text":
                    $result = $this->receiveText($postObj);
                    break;
//                case "image":
//                    $result = $this->receiveImage($postObj);
//                    break;
//                case "location":
//                    $result = $this->receiveLocation($postObj);
//                    break;
//                case "voice":
//                    $result = $this->receiveVoice($postObj);
//                    break;
//                case "video":
//                    $result = $this->receiveVideo($postObj);
//                    break;
//                case "link":
//                    $result = $this->receiveLink($postObj);
//                    break;
                default:
                    $result = "unknown msg type: ".$RX_TYPE;
                    break;
            }
            $this->logger("T ".$result);
            echo $result;
        }else {
            echo "";
            exit;
        }
    }


    //接收事件消息
    private function receiveEvent($object)
    {
        $weixin = new Jssdk(C('WX_CONFIG')['APPID'], C('WX_CONFIG')['APPSECRET']);
        $openid = strval($object->FromUserName);
        $content = "";

        switch ($object->Event)
        {
            case "subscribe":
                $info = $weixin->get_user_info($openid);
                $municipalities = array("北京", "上海", "天津", "重庆", "香港", "澳门");
                $sexes = array("", "男", "女");
                $data = array();
                $data['openid'] = $openid;
                $data['nickname'] = str_replace("'", "", $info['nickname']);
                $data['sex'] = $sexes[$info['sex']];
                $data['country'] = $info['country'];
                $data['province'] = $info['province'];
                $data['city'] = (in_array($info['province'], $municipalities))?$info['province'] : $info['city'];
                $data['scene'] = (isset($object->EventKey) && (stripos(strval($object->EventKey),"qrscene_")))?str_replace("qrscene_","",$object->EventKey):"0";

                $data['headimgurl'] = $info['headimgurl'];
                $data['subscribe'] = $info['subscribe_time'];
                $data['heartbeat'] = time();
                $data['remark'] = $info['remark'];
                $data['score'] = 1;
                $data['tagid'] = $info['tagid_list'];
                /**
                 *  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '序号',
                `openid` varchar(30) NOT NULL COMMENT '微信openid',
                `nickname` varchar(20) NOT NULL COMMENT '昵称',
                `sex` varchar(2) NOT NULL COMMENT '性别',
                `country` varchar(10) NOT NULL COMMENT '国家',
                `province` varchar(16) NOT NULL COMMENT '省份',
                `city` varchar(16) NOT NULL COMMENT '城市',
                `latitude` float(10,6) NOT NULL COMMENT '纬度',
                `longitude` float(10,6) NOT NULL COMMENT '经度',
                `headimgurl` varchar(200) NOT NULL COMMENT '头像',
                `latest` varchar(100) NOT NULL COMMENT '最后互动',
                 */
                //插入wx_user表
                $isSubscribe = M('wx_user') -> where(array('openid'=>$openid))->count()?true:false;
                if(!$isSubscribe){
                    M('wx_user') -> add($data);
                }
                $scene = substr($object->EventKey,8);
                if($scene){//场景关注
                    $data['openid'] = $openid;
                    $data['studio_id'] = intval($scene);
                    $data['create_time'] = time();
                    $data['is_default'] = 1;
                    $result = M('studio_binding') -> add($data);
                }
                $content = $info['nickname']."您来啦，感谢您关注美尚云平台!";
                $content .= "美尚云是广东美尚网络科技有限公司打造的中国美容业领先的采购平台，";
                $content .= "聚焦医美抗衰和药妆美容项目产品的采购业务，";
                $content .= "通过联合采购模式帮助全国中小型美容院减低采购成本，实行更好的利润规划；";
                $content .= "同时美尚云作为行业生态圈平台通过大众创业万众创新机制帮助从业人员实行创业创富，";
                $content .= "弹指之间把资源转化为财富，帮平台用户巧赚钱！".$scene;
                // $weixin->send_custom_message($openid, "text", $content.time());

                break;
            case "unsubscribe":
                //删除不关注用户
                M('wx_user') -> where(array('openid'=>$openid))->delete();
                // $User->where("`openid` = '".$openid."'")->delete();
                // $data['heartbeat'] = 0;
                // $User->where("`openid` = '".$openid."'")->save($data); // 根据条件更新记录
                break;
//            case "CLICK":
//                switch ($object->EventKey)
//                {
//                    case "TEXT":
//                        $content = "微笑：/::)\n乒乓：/:oo\n中国：".$this->bytes_to_emoji(0x1F1E8).$this->bytes_to_emoji(0x1F1F3)."\n仙人掌：".$this->bytes_to_emoji(0x1F335);
//                        break;
//                    case "SINGLENEWS":
//                        $content = array();
//                        $content[] = array("Title"=>"单图文标题",  "Description"=>"单图文内容", "PicUrl"=>"http://images2015.cnblogs.com/blog/340216/201605/340216-20160515215306820-740762359.jpg", "Url" =>"http://m.cnblogs.com/?u=txw1958");
//                        break;
//                    case "MULTINEWS":
//                        $content = array();
//                        $content[] = array("Title"=>"多图文1标题", "Description"=>"", "PicUrl"=>"http://images2015.cnblogs.com/blog/340216/201605/340216-20160515215306820-740762359.jpg", "Url" =>"http://m.cnblogs.com/?u=txw1958");
//                        $content[] = array("Title"=>"多图文2标题", "Description"=>"", "PicUrl"=>"http://d.hiphotos.bdimg.com/wisegame/pic/item/f3529822720e0cf3ac9f1ada0846f21fbe09aaa3.jpg", "Url" =>"http://m.cnblogs.com/?u=txw1958");
//                        $content[] = array("Title"=>"多图文3标题", "Description"=>"", "PicUrl"=>"http://g.hiphotos.bdimg.com/wisegame/pic/item/18cb0a46f21fbe090d338acc6a600c338644adfd.jpg", "Url" =>"http://m.cnblogs.com/?u=txw1958");
//                        break;
//                    case "MUSIC":
//                        $content = array();
//                        $content = array("Title"=>"最炫民族风", "Description"=>"歌手：凤凰传奇", "MusicUrl"=>"http://mascot-music.stor.sinaapp.com/zxmzf.mp3", "HQMusicUrl"=>"http://mascot-music.stor.sinaapp.com/zxmzf.mp3");
//                        break;
//                    default:
//                        $content = "点击菜单：".$object->EventKey;
//                        break;
//                }
//                break;
//            case "VIEW":
//                $content = "跳转链接 ".$object->EventKey;
//                break;
            case "SCAN":
                $studio_id = intval($object->EventKey);
                $studioInfo = M('studio') -> where(array('id'=>$studio_id))->find();
                $isSubscribe = M('studio_binding') -> where(array('studio_id'=>$studio_id,'openid'=>$openid))->count();
                if($isSubscribe){
                    $content = "你已绑定".$studioInfo['name'];
                    break;
                }
                $isSubscribe = M('studio_binding') -> where(array('openid'=>$openid))->count();
                $data['openid'] = $openid;
                $data['studio_id'] = $studio_id;
                $data['create_time'] = time();
                if(!$isSubscribe){
                    $data['is_default'] = 1;
                }
                $result = M('studio_binding') -> add($data);
                if($result){
                    $content = "您成功绑定".$studioInfo['name'];
                }else{
                    $content = "绑定".$studioInfo['name'].'失败！';
                }
                break;
//            case "LOCATION":
//
//                $content = "上传位置：纬度 ".$object->Latitude.";经度 ".$object->Longitude;
//                $content = "";
//                break;
//            case "scancode_waitmsg":
//                if ($object->ScanCodeInfo->ScanType == "qrcode"){
//                    $content = "扫码带提示：类型 二维码 结果：".$object->ScanCodeInfo->ScanResult;
//                }else if ($object->ScanCodeInfo->ScanType == "barcode"){
//                    $codeinfo = explode(",",strval($object->ScanCodeInfo->ScanResult));
//                    $codeValue = $codeinfo[1];
//                    $content = "扫码带提示：类型 条形码 结果：".$codeValue;
//                }else{
//                    $content = "扫码带提示：类型 ".$object->ScanCodeInfo->ScanType." 结果：".$object->ScanCodeInfo->ScanResult;
//                }
//                break;
//            case "scancode_push":
//                $content = "扫码推事件";
//                break;
//            case "pic_sysphoto":
//                $content = "系统拍照";
//                break;
//            case "pic_weixin":
//                $content = "相册发图：数量 ".$object->SendPicsInfo->Count;
//                break;
//            case "pic_photo_or_album":
//                $content = "拍照或者相册：数量 ".$object->SendPicsInfo->Count;
//                break;
//            case "location_select":
//                $content = "发送位置：标签 ".$object->SendLocationInfo->Label;
//                break;
//            default:
//                $content = "receive a new event: ".$object->Event;
//                break;
        }
        if(is_array($content)){
            if (isset($content[0])){
                $result = $this->transmitNews($object, $content);
            }else if (isset($content['MusicUrl'])){
                $result = $this->transmitMusic($object, $content);
            }
        }else{
            $result = $this->transmitText($object, $content);
        }

        return $result;
    }

    //接收文本消息
//    private function receiveText($object)
//    {
//        $keyword = trim($object->Content);
//        $openid = strval($object->FromUserName);
//        $content = "";
//
//        //多客服人工回复模式
//        if (strstr($keyword, "在线客服_") || strstr($keyword, "你好_")){
//            $result = $this->transmitService($object);
//        }
//        //自动回复模式
//        else{
//            if (strstr($keyword, "文本")){
//                $content = "这是个文本消息\n".$openid;
//
//            }else if (strstr($keyword, "单图文")){
//                $content = array();
//                $content[] = array("Title"=>"单图文标题",  "Description"=>"单图文内容", "PicUrl"=>"http://files.cnblogs.com/files/txw1958/cartoon.gif", "Url" =>"http://m.cnblogs.com/?u=txw1958");
//                $weixin = new Jssdk(C('WX_CONFIG')['APPID'], C('WX_CONFIG')['APPSECRET']);
//                $template = array('touser' => $openid,
//                    'template_id' => "_yFpVtfHd0pSWy6ffApi6isjY8HmmWC8aKW-Uqz8viU",
//                    'url' => "http://www.baidu.com/",
//                    'topcolor' => "#0000C6",
//                    'data' => array('content'    => array('value' => "你妈妈\\n喊你\\n回家吃饭了！",
//                        'color' => "#743A3A",
//                    ),
//                    )
//                );
//                $weixin->send_template_message($template);
//            }else if (strstr($keyword, "图文") || strstr($keyword, "多图文")){
//                $content = array();
//                $content[] = array("Title"=>"多图文1标题", "Description"=>"", "PicUrl"=>"http://files.cnblogs.com/files/txw1958/cartoon.gif", "Url" =>"http://m.cnblogs.com/?u=txw1958");
//                $content[] = array("Title"=>"多图文2标题", "Description"=>"", "PicUrl"=>"http://d.hiphotos.bdimg.com/wisegame/pic/item/f3529822720e0cf3ac9f1ada0846f21fbe09aaa3.jpg", "Url" =>"http://m.cnblogs.com/?u=txw1958");
//                $content[] = array("Title"=>"多图文3标题", "Description"=>"", "PicUrl"=>"http://g.hiphotos.bdimg.com/wisegame/pic/item/18cb0a46f21fbe090d338acc6a600c338644adfd.jpg", "Url" =>"http://m.cnblogs.com/?u=txw1958");
//
//            }else if (strstr($keyword, "音乐")){
//                $content = array();
//                $content = array("Title"=>"最炫民族风", "Description"=>"歌手：凤凰传奇", "MusicUrl"=>"http://121.199.4.61/music/zxmzf.mp3", "HQMusicUrl"=>"http://121.199.4.61/music/zxmzf.mp3");
//            }else{
//                // $content = date("Y-m-d H:i:s",time())."\n".$object->FromUserName."";
//                $content = $this->xiaoirobot($openid, $keyword);
//            }
//
//            if(is_array($content)){
//                if (isset($content[0]['PicUrl'])){
//                    $result = $this->transmitNews($object, $content);
//                }else if (isset($content['MusicUrl'])){
//                    $result = $this->transmitMusic($object, $content);
//                }
//            }else{
//                $result = $this->transmitText($object, $content);
//            }
//        }
//
//        return $result;
//    }

//    private function AttentionReply1(){
//        $textTpl=" <xml>
//                    <ToUserName><![CDATA[%s]]></ToUserName>
//                    <FromUserName><![CDATA[%s]]></FromUserName>
//                    <CreateTime>%s</CreateTime>
//                    <MsgType><![CDATA[news]]></MsgType>
//                    <ArticleCount>1</ArticleCount>
//                    <Articles>
//                        <item>
//                            <Title><![CDATA[%s]]></Title>
//                            <Description><![CDATA[%s]]></Description>
//                            <PicUrl><![CDATA[%s]]></PicUrl>
//                            <Url><![CDATA[%s]]></Url>
//                        </item>
//                    </Articles>
//                    <FuncFlag>1</FuncFlag>
//                </xml>";
//        $title1="美尚云欢迎您";
//        $Description1="亲，欢迎您关注美尚云，";
//        $PicUrl1="http://mch.meishangyun.com/Uploads/myms/goods-main-img/1503293931.jpeg";
//        $Url1="http://mch.meishangyun.com";
//        $resultStr = sprintf($textTpl, $this->fromUsername, $this->toUsername, $this->time,$title1,$Description1,$PicUrl1,$Url1);
//        echo $resultStr;
//    }

    //接收图片消息
    private function receiveImage($object)
    {
        $content = array("MediaId"=>$object->MediaId);
        $result = $this->transmitImage($object, $content);
        return $result;
    }

    //接收位置消息
    private function receiveLocation($object)
    {

        $content = "你发送的是位置，经度为：".$object->Location_Y."；纬度为：".$object->Location_X."；缩放级别为：".$object->Scale."；位置为：".$object->Label;
        $result = $this->transmitText($object, $content);
        return $result;
    }

    //接收语音消息
    private function receiveVoice($object)
    {
        if (isset($object->Recognition) && !empty($object->Recognition)){
            $content = "你刚才说的是：".$object->Recognition;
            $result = $this->transmitText($object, $content);
        }else{
            $content = array("MediaId"=>$object->MediaId);
            $result = $this->transmitVoice($object, $content);
        }
        return $result;
    }

    //接收视频消息
    private function receiveVideo($object)
    {
        $content = array("MediaId"=>$object->MediaId, "ThumbMediaId"=>$object->ThumbMediaId, "Title"=>"", "Description"=>"");
        $result = $this->transmitVideo($object, $content);
        return $result;
    }

    //接收链接消息
    private function receiveLink($object)
    {
        $content = "你发送的是链接，标题为：".$object->Title."；内容为：".$object->Description."；链接地址为：".$object->Url;
        $result = $this->transmitText($object, $content);
        return $result;
    }

    //回复文本消息
    private function transmitText($object, $content)
    {
        if (!isset($content) || empty($content)){
            return "";
        }
        $xmlTpl = "<xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%s</CreateTime>
<MsgType><![CDATA[text]]></MsgType>
<Content><![CDATA[%s]]></Content>
</xml>";
        $result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time(), $content);
        return $result;
    }

    //回复图文消息
    private function transmitNews($object, $newsArray)
    {
        if(!is_array($newsArray)){
            return "";
        }
        $itemTpl = "<item>
         <Title><![CDATA[%s]]></Title>
        <Description><![CDATA[%s]]></Description>
        <PicUrl><![CDATA[%s]]></PicUrl>
        <Url><![CDATA[%s]]></Url>
        </item>";
        $item_str = "";
        foreach ($newsArray as $item){
            $item_str .= sprintf($itemTpl, $item['Title'], $item['Description'], $item['PicUrl'], $item['Url']);
        }
        $xmlTpl = "<xml>
        <ToUserName><![CDATA[%s]]></ToUserName>
        <FromUserName><![CDATA[%s]]></FromUserName>
        <CreateTime>%s</CreateTime>
        <MsgType><![CDATA[news]]></MsgType>
        <ArticleCount>%s</ArticleCount>
        <Articles>$item_str</Articles>
        </xml>";

        $result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time(), count($newsArray));
        return $result;
    }

    //回复音乐消息
    private function transmitMusic($object, $musicArray)
    {
        $itemTpl = "<Music>
    <Title><![CDATA[%s]]></Title>
    <Description><![CDATA[%s]]></Description>
    <MusicUrl><![CDATA[%s]]></MusicUrl>
    <HQMusicUrl><![CDATA[%s]]></HQMusicUrl>
</Music>";

        $item_str = sprintf($itemTpl, $musicArray['Title'], $musicArray['Description'], $musicArray['MusicUrl'], $musicArray['HQMusicUrl']);

        $xmlTpl = "<xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%s</CreateTime>
<MsgType><![CDATA[music]]></MsgType>
$item_str
</xml>";

        $result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time());
        return $result;
    }

    //回复图片消息
    private function transmitImage($object, $imageArray)
    {
        $itemTpl = "<Image>
        <MediaId><![CDATA[%s]]></MediaId>
    </Image>";

        $item_str = sprintf($itemTpl, $imageArray['MediaId']);

        $xmlTpl = "<xml>
    <ToUserName><![CDATA[%s]]></ToUserName>
    <FromUserName><![CDATA[%s]]></FromUserName>
    <CreateTime>%s</CreateTime>
    <MsgType><![CDATA[image]]></MsgType>
    $item_str
</xml>";

        $result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time());
        return $result;
    }

    //回复语音消息
    private function transmitVoice($object, $voiceArray)
    {
        $itemTpl = "<Voice>
        <MediaId><![CDATA[%s]]></MediaId>
    </Voice>";

        $item_str = sprintf($itemTpl, $voiceArray['MediaId']);
        $xmlTpl = "<xml>
    <ToUserName><![CDATA[%s]]></ToUserName>
    <FromUserName><![CDATA[%s]]></FromUserName>
    <CreateTime>%s</CreateTime>
    <MsgType><![CDATA[voice]]></MsgType>
    $item_str
</xml>";

        $result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time());
        return $result;
    }

    //回复视频消息
    private function transmitVideo($object, $videoArray)
    {
        $itemTpl = "<Video>
        <MediaId><![CDATA[%s]]></MediaId>
        <ThumbMediaId><![CDATA[%s]]></ThumbMediaId>
        <Title><![CDATA[%s]]></Title>
        <Description><![CDATA[%s]]></Description>
    </Video>";

        $item_str = sprintf($itemTpl, $videoArray['MediaId'], $videoArray['ThumbMediaId'], $videoArray['Title'], $videoArray['Description']);

        $xmlTpl = "<xml>
    <ToUserName><![CDATA[%s]]></ToUserName>
    <FromUserName><![CDATA[%s]]></FromUserName>
    <CreateTime>%s</CreateTime>
    <MsgType><![CDATA[video]]></MsgType>
    $item_str
</xml>";

        $result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time());
        return $result;
    }

    //回复多客服消息
    private function transmitService($object)
    {
        $xmlTpl = "<xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%s</CreateTime>
<MsgType><![CDATA[transfer_customer_service]]></MsgType>
</xml>";
        $result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time());
        return $result;
    }

    //机器人回复
    function xiaoirobot($openid, $content)
    {
        //定义app
        $app_key = "";
        $app_secret = "";

        //签名算法
        $realm = "xiaoi.com";
        $method = "POST";
        $uri = "/robot/ask.do";
        $nonce = "";
        $chars = "abcdefghijklmnopqrstuvwxyz0123456789";
        for ($i = 0; $i < 40; $i++) {
            $nonce .= $chars[ mt_rand(0, strlen($chars) - 1) ];
        }
        $HA1 = sha1($app_key.":".$realm.":".$app_secret);
        $HA2 = sha1($method.":".$uri);
        $sign = sha1($HA1.":".$nonce.":".$HA2);

        //接口调用
        $url = "http://nlp.xiaoi.com/robot/ask.do";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('X-Auth:	app_key="'.$app_key.'", nonce="'.$nonce.'", signature="'.$sign.'"'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "question=".urlencode($content)."&userId=".$openid."&platform=custom&type=0");
        $output = curl_exec($ch);
        if ($output === FALSE){
            return "cURL Error: ". curl_error($ch);
        }
        return trim($output);
    }

    //字节转Emoji表情
    function bytes_to_emoji($cp)
    {
        if ($cp > 0x10000){       # 4 bytes
            return chr(0xF0 | (($cp & 0x1C0000) >> 18)).chr(0x80 | (($cp & 0x3F000) >> 12)).chr(0x80 | (($cp & 0xFC0) >> 6)).chr(0x80 | ($cp & 0x3F));
        }else if ($cp > 0x800){   # 3 bytes
            return chr(0xE0 | (($cp & 0xF000) >> 12)).chr(0x80 | (($cp & 0xFC0) >> 6)).chr(0x80 | ($cp & 0x3F));
        }else if ($cp > 0x80){    # 2 bytes
            return chr(0xC0 | (($cp & 0x7C0) >> 6)).chr(0x80 | ($cp & 0x3F));
        }else{                    # 1 byte
            return chr($cp);
        }
    }

    //日志记录
    private function logger($log_content)
    {
        if(isset($_SERVER['HTTP_APPNAME'])){   //SAE
            sae_set_display_errors(false);
            sae_debug($log_content);
            sae_set_display_errors(true);
        }else if($_SERVER['REMOTE_ADDR'] != "127.0.0.2"){ //LOCAL
            $max_size = 1000000;
            $log_filename = "log.xml";
            if(file_exists($log_filename) and (abs(filesize($log_filename)) > $max_size)){unlink($log_filename);}
            file_put_contents($log_filename, date('H:i:s')." ".$log_content."\r\n", FILE_APPEND);
        }
    }
}

