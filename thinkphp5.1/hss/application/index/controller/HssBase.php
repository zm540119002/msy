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
                $data['city'] = (in_array($weixinUserInfo['province'], $municipalities))?$weixinUserInfo['province'] : $weixinUserInfo['city'];
                $data['headimgurl'] = $weixinUserInfo['headimgurl'];
                if($info && !$info['headimgurl']){
                    $data['id'] = $info['id'];
                }
                $id = $model->edit($data);
                if(!$id){
                    return errorMsg('失败');
                }
            }
            $user = checkLogin();
            //修改用户表
            if((!$user['name'] || !$user['avatar']) && $user && isset($weixinUserInfo['openid'])){
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
                if(false===$result){

                }else{
                    setSession($user);
                }
            }
            //openid 关联 平台user_id
            if($user && !$user['openid'] && !$info['user_id']){
                $model = new \app\index\model\WeixinUser();
                $where = [
                    ['openid','=',$openid]
                ];
                $data = [
                    'user_id'=>$user['id']
                ];
                $result = $model->isUpdate(true)->save($data,$where);
                if(false===$result){

                }else{
                    $user['openid'] = $openid;
                    setSession($user);
                }
            }

            //判断是否关注平台
            if(empty($info) || !$info['subscribe']){
                 //没有关注
                $this -> assign('subscribe',1);
            }

            //微信分享初始化
            $weixnTools = new \common\component\payment\weixin\Jssdk(config('wx_config.appid'), config('wx_config.appsecret'));
            $signPackage =  $weixnTools->GetSignPackage();
            $this->assign('signPackage',$signPackage);

        }
    }
}