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

    public function jin1(){

        $file = 'static/index/js/mobileSelector/js/json1.js';
        $data = file_get_contents($file);
        $data = json_decode($data,true);

        $province = [];
        foreach ($data as $k => $v){

            if($v['level']==1){
                $v1['area_id'] = $v['id'];
                $v1['province_name'] = $v['name'];
                $v1['city_name'] = $v['shortName'];
                $v1['area_parent_id'] = $v['parentId'];
                $province[$v['id']] = $v1;
            }

        }

        $city = [];
        foreach ($data as $k => $v){

            if($v['level']==2){
                $v1['area_id'] = $v['id'];
                $v1['province_name'] = $province[$v['parentId']]['province_name'];
                $v1['city_name'] = $v['name'];
                $v1['area_parent_id'] = $v['parentId'];
                $city[] = $v1;
            }
        }

        $model = new \app\index\model\CityArea();
        $res = $model->editAll($city);
        //p($data);
        p($res);
        echo '<hr>';
        //p($province);
        exit;

        $data = input('post.');

        p($data);
        exit;
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