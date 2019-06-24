<?php
namespace app\index\controller;

/**
 * 平台基类
 */
class HssBase extends \common\controller\Base{
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

            $weixinUserInfo =  session('weixinUserInfo');
            if(!$weixinUserInfo){
                $weixnTools = new \common\component\payment\weixin\Jssdk(config('wx_config.appid'), config('wx_config.appsecret'));
                $weixinUserInfo = $weixnTools->getOauthUserInfo();
                session('weixinUserInfo',$weixinUserInfo);
            }
            $openid = $weixinUserInfo['openid'];
            //保存获取微信用户表的信息
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
            /**
             *  [openid] => oxFkSs_72n49f1my5YDDKpt4EMtA
            [nickname] => 杨观保
            [sex] => 1
            [language] => zh_CN
            [city] => 深圳
            [province] => 广东
            [country] => 中国
            [headimgurl] => http://thirdwx.qlogo.cn/mmopen/vi_32/YsGBcc3ZDjXFOGGCG6KTSTxTJY39nNLbibPHW3iaex8U9WQatoTfz2bPUQOM9d7NCE265NmoZ1mCEarcn6uGb4Zw/132
            [privilege] => Array
            (
            )

            [unionid] => oHg2ht3eC4OgmdAHaUldzp7SsQUA
            )
             */
            //没有获取到微信的信息
            if(($info && !$info['headimgurl']) || empty($info)){
                $municipalities = array("北京", "上海", "天津", "重庆", "香港", "澳门");
                $sexes = array("", "男", "女");
                $data = array();
                $data['openid'] = $weixinUserInfo['openid'];
                $data['nickname'] = str_replace("'", "", $weixinUserInfo['nickname']);
                $data['sex'] = $sexes[$weixinUserInfo['sex']];
                $data['country'] = $weixinUserInfo['country'];
                $data['province'] = $weixinUserInfo['province'];
                $data['city'] = (in_array($weixinUserInfo['province'], $municipalities))?$weixinUserInfo['province'] : $info['city'];
                $data['headimgurl'] = $weixinUserInfo['headimgurl'];
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
                    $weixinAvatarUrl = $weixinUserInfo['headimgurl'];
                    $avatar = saveImageFromHttp($weixinAvatarUrl,$relativeSavePath);
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
            //判断是否关注平台
            if(empty($info) || !$info['subscribe']){
                 //没有关注
                $this -> assign('subscribe',1);
            }

        }
    }
}