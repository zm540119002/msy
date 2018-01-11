<?php
namespace web\all\Controller;
use Think\Controller;
use web\all\Component\WxpayAPI\Jssdk;
/**公共基础控制器
 */
class CommonController extends Controller{
    private $_jssdk = null;
    protected $host;
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
            return  $this->_jssdk ->getOauthUserInfo();
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

    //微信登录授权获取用户
    public function wxLogin(){
        $wechat= new Jssdk(C('WX_CONFIG')['APPID'], C('WX_CONFIG')['APPSECRET']);
        $code = isset($_GET['code'])?$_GET['code']:'';
        $scope = 'snsapi_userinfo';
        if($code){
            $wxUser = $this -> getOAuthWeiXinUserInfo();
            if(!$wxUser){
                return false;
            }else{
                return $wxUser;
            }
        }else{
            //开始获取code
            if($scope == 'snsapi_userinfo'){
                $url = 'http://'.$this->host . $_SERVER['REQUEST_URI'];
                $_SESSION['wx_redirect'] = $url;
            }else{
                $url = $_SESSION['wx_redirect'];
            }
            if(!$url){
                unset($_SESSION['wx_redirect']);
                return false;
            }
            $wechat -> getOauthRedirect($url,"wxbase");
        }

    }
}


