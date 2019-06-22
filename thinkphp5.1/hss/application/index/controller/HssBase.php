<?php
namespace app\index\controller;

/**
 * 平台基类
 */
class HssBase extends \common\controller\UserBase{

    protected $wallet = null;

    public function __construct(){
        parent::__construct();
        //微信处理
        if(isWxBrowser() && !request()->isAjax()) {//判断是否为微信浏览器
            /**
             *   `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '序号',
            `user_id` int(11) NOT NULL DEFAULT '0' COMMENT '平台user_id',
            `referee` int(11) NOT NULL DEFAULT '0' COMMENT '推荐人',
            `openid` varchar(30) NOT NULL DEFAULT '' COMMENT '微信id',
            `nickname` varchar(20) NOT NULL DEFAULT '' COMMENT '昵称',
            `remark` varchar(20) NOT NULL DEFAULT '' COMMENT '备注',
            `sex` varchar(4) NOT NULL DEFAULT '' COMMENT '性别',
            `country` varchar(10) NOT NULL DEFAULT '' COMMENT '国家',
            `province` varchar(16) NOT NULL DEFAULT '' COMMENT '省份',
            `city` varchar(16) NOT NULL DEFAULT '' COMMENT '城市',
            `headimgurl` varchar(200) NOT NULL DEFAULT '' COMMENT '头像',
            `heartbeat` varchar(15) NOT NULL DEFAULT '' COMMENT '最后心跳',
            `subscribe` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0：没关注 1：关注',
            `subscribe_scene` varchar(20) NOT NULL DEFAULT '' COMMENT '返回用户关注的渠道来源，ADD_SCENE_SEARCH 公众号搜索，ADD_SCENE_ACCOUNT_MIGRATION 公众号迁移，ADD_SCENE_PROFILE_CARD 名片分享，ADD_SCENE_QR_CODE 扫描二维码，ADD_SCENEPROFILE LINK 图文页内名称点击，ADD_SCENE_PROFILE_ITEM 图文页右上角菜单，ADD_SCENE_PAID 支付后关注，ADD_SCENE_OTHERS 其他',
            `subscribe_time` varchar(20) NOT NULL DEFAULT '' COMMENT '关注时间',
             */
            $weixinTools = new \common\component\payment\weixin\Jssdk(config('wx_config.appid'), config('wx_config.appsecret'));
            //获取微信auto2AccessToken和openid
            $accessTokenAndOpenid = $weixinTools -> getAccessTokenAndOpenid();
            $openid = $accessTokenAndOpenid['openid'];
            $accessToken = $accessTokenAndOpenid['access_token'];
            //获取微信用户表的信息
            $model = new \app\index\model\WeixinUser();
            $config = [
                'where' => [
                    ['openid','=',$openid]
                ],'field'=>[
                    'id','user_id',
                    'referee','headimgurl','subscribe','nickname'
                ]
            ];
            $info = $model -> getInfo($config);
            p($info);exit;
            //判断是否关注平台
            if(empty($info) || !$info['subscribe']){
                 //没有关注
                $this -> assign('subscribe',1);
            }
            //没有获取到微信的信息
            if(($info && !$info['headimgurl']) || empty($info)){
                $weixinUserInfo = $weixinTools -> oauth2_get_user_info($accessToken, $openid);
                $municipalities = array("北京", "上海", "天津", "重庆", "香港", "澳门");
                $sexes = array("", "男", "女");
                $data = array();
                $data['openid'] = $weixinUserInfo['openid'];
                $data['nickname'] = str_replace("'", "", $weixinUserInfo['nickname']);
                $data['sex'] = $sexes[$weixinUserInfo['sex']];
                $data['country'] = $weixinUserInfo['country'];
                $data['province'] = $weixinUserInfo['province'];
                $data['city'] = (in_array($weixinUserInfo['province'], $municipalities))?$weixinUserInfo['province'] : $info['city'];
                $data['subscribe_scene'] = $weixinUserInfo['subscribe_scene'];
                $data['headimgurl'] = $weixinUserInfo['headimgurl'];
                $data['subscribe'] = $weixinUserInfo['subscribe'];
                $data['subscribe_time'] = $weixinUserInfo['subscribe_time'];
                $data['heartbeat'] = time();
                $data['remark'] = $weixinUserInfo['remark'];
                $data['referee'] = $weixinUserInfo['qr_scene']; //带参场景关注类型
                if($info && !$info['headimgurl']){
                    $data['id'] = $info['id'];
                }
                $id = $model->edit($data);
                if(!$id){
                    return errorMsg('失败');
                }

                //修改用户表
                $user = checkLogin();
                if((!$user['name'] || !$user['avatar']) && $user && isset($weiXinUserInfo['openid'])){
                    //临时相对路径
                    $relativeSavePath = config('upload_dir.user_avatar');
                    $weiXinAvatarUrl = $weixinUserInfo['headimgurl'];
                    $avatar = saveImageFromHttp($weiXinAvatarUrl,$relativeSavePath);
                    $data = [
                        'id'=>$user['id'],
                        'name'=>$weixinUserInfo['nickname'],
                        'avatar'=>$avatar,
                    ];
                    if($user['avatar']){
                        unset($data['avatar']);
                    }else{
                        $user['avatar'] = $data['avatar'];
                    }
                    if($user['name']){
                        unset($data['name']);
                    }else{
                        $user['name'] = $data['name'];
                    }
                    $userModel = new \common\model\User();
                    $result = $userModel->isUpdate(true)->save($data);
                    setSession($user);
                }
            }
        }
    }
}