<?php
namespace common\controller;

/**用户信息验证控制器基类
 */
class UserBase extends Base{
    protected $user = null;
    protected $indexUrl = 'index/Index/index';//平台首页
    
    public function __construct(){
        parent::__construct();
        //判断是否登录
        $this->user = checkLogin();
        if (!$this->user) {
            if(request()->isAjax()){
                $this->errorMsg(config('code.error.login.msg'),config('code.error.login'));
            }else{
                echo $this->fetch('../../api/public/template/login_page.html');
                exit;
            }
        }
        //微信处理
//        if(isWxBrowser() && !request()->isAjax()) {//判断是否为微信浏览器
//            $weiXinUserInfo =  session('weiXinUserInfo');
//            if(!$weiXinUserInfo){
//                $mineTools = new \common\component\payment\weixin\Jssdk(config('wx_config.appid'), config('wx_config.appsecret'));
//                $weiXinUserInfo = $mineTools->getOauthUserInfo();
//                session('weiXinUserInfo',$weiXinUserInfo);
//            }
//            $user = checkLogin();
//            if((!$user['name'] || !$user['avatar']) && $user && isset($weiXinUserInfo['openid'])){
//                //临时相对路径
//                $relativeSavePath = config('upload_dir.user_avatar');
//                $weiXinAvatarUrl = $weiXinUserInfo['headimgurl'];
//                $avatar = saveImageFromHttp($weiXinAvatarUrl,$relativeSavePath);
//                $data = [
//                    'id'=>$user['id'],
//                    'name'=>$weiXinUserInfo['nickname'],
//                    'avatar'=>$avatar,
//                ];
//                if($user['avatar']){
//                    unset($data['avatar']);
//                }else{
//                    $user['avatar'] = $data['avatar'];
//                }
//                if($user['name']){
//                    unset($data['name']);
//                }else{
//                    $user['name'] = $data['name'];
//                }
//                $userModel = new \common\model\User();
//                $result = $userModel->isUpdate(true)->save($data);
//                setSession($user);
//            }
//
//        }

    }
}