<?php
namespace app\index\controller;

// 前台首页
use think\Console;

class Test extends \common\controller\Base{
    /**首页
     */
    public function index(){
        if(request()->isAjax()){
        }else{
            return $this->fetch();
        }
    }
    /**测试
     */
    public function test(){
        if(request()->isAjax()){
        }else{
            return $this->fetch();
        }
    }
    /**测试-城市
     */
    public function city(){
        if(request()->isAjax()){
        }else{
            return $this->fetch();
        }
    }
    /**测试-城市2
     */
    public function city2(){
        if(request()->isAjax()){
        }else{
            return $this->fetch();
        }
    }
    /**测试-城市3
     */
    public function city3(){
        if(request()->isAjax()){
        }else{
            return $this->fetch();
        }
    }

    public function jin(){

        return $this->fetch();

    }

    public function weixin()
    {
        $mineTools = new \common\component\payment\weixin\Jssdk(config('wx_config.appid'), config('wx_config.appsecret'));
        $weiXinUserInfo1 = $mineTools->getOauthUserInfo();
        P($weiXinUserInfo1);
//        $weiXinUserInfo2= $mineTools->get_user_info($weiXinUserInfo1['openid']);
//        P($weiXinUserInfo2);
    }
}