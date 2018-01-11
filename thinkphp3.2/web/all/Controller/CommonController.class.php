<?php
namespace web\all\Controller;
use Think\Controller;
use web\all\Component\WxpayAPI\Jssdk;


/**公共基础控制器
 */
class CommonController extends Controller{
    private $_jssdk = null;
    protected $host;
    protected $signPackage;
    public function __construct(){
        parent::__construct();
        $this->host = isset($_SERVER['HTTP_X_FORWARDED_HOST']) ? $_SERVER['HTTP_X_FORWARDED_HOST'] :
            (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '');
        $this->_jssdk = new Jssdk(C('WX_CONFIG')['APPID'], C('WX_CONFIG')['APPSECRET']);
        $this -> signPackage = $this -> weiXinShareInit();
    }

    //微信分享初始化
    public function weiXinShareInit(){
        $signPackage =  $this->_jssdk->GetSignPackage();
        return $signPackage;
    }

    //微信分享信息
    public function weiXinShareInfo($title,$shareLink,$shareImgRelativeUrl,$desc,$backUrl){
        $shLink = substr($shareLink,0,strrpos($shareLink,'/share'));
        if(empty($shLink)){
            $shLink = $shareLink;
        }
        $shareLink = $shLink.'.html';
        $host = isset($_SERVER['HTTP_X_FORWARDED_HOST']) ? $_SERVER['HTTP_X_FORWARDED_HOST'] :
            (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '');
        $shareImgUrl = (is_ssl()?'https://':'http://').$host.C('UPLOAD_PATH_PHP').$shareImgRelativeUrl;
        if(empty($backUrl)){
            $backUrl = $shareLink;
        }
        $shareInfo = array(
            'title' => $title,
            'shareLink' => $shareLink,
            'shareImgUrl' => $shareImgUrl,
            'desc' => $desc,
            'backUrl' => $backUrl,
        );


        return $shareInfo;
    }

    //微信分享信息
//    public function weiXinShare($title,$shareLink,$shareImgRelativeUrl,$desc,$backUrl){
    public function weiXinShare($shareInfo){
        $host = isset($_SERVER['HTTP_X_FORWARDED_HOST']) ? $_SERVER['HTTP_X_FORWARDED_HOST'] :
            (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '');
        $shareImgUrl = (is_ssl()?'https://':'http://').$host.C('UPLOAD_PATH_PHP').$shareInfo['shareImgUrl'];
        $shareInfo['shareImgUrl'] = $shareImgUrl;
        $html = <<<EOF
			<script type="text/javascript" src="/Public/js/common/jquery-1.9.1.min.js"></script>
			<script type="text/javascript" src="/Public/js/common/layer.mobile/layer.js"></script>
			<script type="text/javascript" src="/Public/js/common/dialog.js"></script>
		    <script type="text/javascript" src="http://res.wx.qq.com/open/js/jweixin-1.2.0.js"></script>
<script type="text/javascript">
    // 微信配置
    wx.config({
        debug: false,
        appId: "{$this -> signPackage ['appId']}",  //公众号的唯一标识
        timestamp: '{$this -> signPackage ["timestamp"]}',  //生成签名的时间戳
        nonceStr: '{$this -> signPackage ["nonceStr"]}',   //生成签名的随机串
        signature: '{$this -> signPackage ["signature"]}',  //签名
        jsApiList: ['onMenuShareTimeline','onMenuShareAppMessage','getLocation','openLocation'] // 功能列表，我们要使用JS-SDK的什么功能
    });
    wx.checkJsApi({
        jsApiList: [
            'onMenuShareTimeline','onMenuShareAppMessage','getLocation','openLocation'
        ],
        success: function (res) {
            if (res.checkResult.getLocation == false) {
                dialog.error('你的微信版本太低，不支持微信JS接口，请升级到最新的微信版本！');
                return;
            }
        }
    });
    wx.ready(function(){
        // 获取"分享到朋友圈"按钮点击状态及自定义分享内容接口
        wx.onMenuShareTimeline({
            title: '{$shareInfo["title"]}', // 分享标题
            link: '{$shareInfo["shareLink"]}',
            desc: '{$shareInfo["desc"]}',
            imgUrl:'{$shareInfo["shareImgUrl"]}', // 分享图标
            success: function () {
                window.location.href='{$shareInfo["backUrl"]}';
            },
            cancel: function () {
                return false;
            }
        });
        // 获取"分享给朋友"按钮点击状态及自定义分享内容接口
        wx.onMenuShareAppMessage({
            title: '{$shareInfo["title"]}', // 分享标题
            link: '{$shareInfo["shareLink"]}',
            desc: '{$shareInfo["desc"]}',
            imgUrl:'{$shareInfo["shareImgUrl"]}', // 分享图标
            success: function () {
                window.location.href='{$shareInfo["backUrl"]}';
            },
            cancel: function () {
                return false;
            }
        });
    });	
         
EOF;
        echo  $html;
        return $shareInfo;
    }

    //获取微信用户基本信息（已关注用户）
    public function getWeiXinUserInfo(){
        if(isWxBrowser()){//判断是否为微信浏览器
            return  $this->_jssdk ->get_user_info($this->_jssdk->getOpenid());
        }
    }

    //OAuth2 授权获取用户基本信息（OAuth2 授权的 Access Token 获取 未关注用户，Access Token为临时获取）
    public function getOAuthWeiXinUserInfo(){
        if(isWxBrowser()){//判断是否为微信浏览器
            return  $this->_jssdk ->getUser();
        }
    }

    //获取微信用户列表信息
    public function getWeiXinUserList(){
        if(isWxBrowser()) {//判断是否为微信浏览器
            return  $this->_jssdk ->getUserList();
        }
    }
    //
    public function getQRcode($scene_type, $scene_id){
        if(isWxBrowser()) {//判断是否为微信浏览器
            return  $this->_jssdk ->create_qrcode($scene_type, $scene_id);
        }
    }

    //自定义菜单
    public function  create_menu_raw($menu){
        /**
         *        $menu = '{
        "button":[
        {
        "type":"view",
        "name":"美容机构",
        "url":"http://m.meishangyun.com/sys_mrjg/do.php"

        },
        {
        "type":"view",
        "name":"美容机构",
        "url":"http://m.meishangyun.com/sys_employee/do.php"
        }]
        }';
         */
        return  $this->_jssdk -> create_menu_raw($menu);
    }
}


