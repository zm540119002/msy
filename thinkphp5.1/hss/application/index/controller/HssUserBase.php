<?php
namespace app\index\controller;

/**
 * 平台基类
 */
class HssUserBase extends \common\controller\UserBase {
    public function __construct(){
        parent::__construct();
        //微信处理
        if(isWxBrowser() && !request()->isAjax()) {//判断是否为微信浏览器
            $weixinUserInfo =  session('weixinUserInfo');
            $openid = $weixinUserInfo['openid'];
            print_r($weixinUserInfo);
            if(!$this->user['openid']){
                echo 11;
                $model = new \app\index\model\WeixinUser();
                $where = [
                   ['openid','=',$openid]
                ];
                $data = [
                    'user_id'=>$this->user['id']
                ];
                $result = $model->isUpdate(true)->save($data,$where);
                if(false===$result){

                }else{
                    $this->user['openid'] = $openid;
                    setSession($this->user);
                }

            }


        }
    }
}