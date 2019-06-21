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
    /**测试-布局
     */
    public function layout(){
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
        $info = $mineTools->getUserInfo();
        p($info);
        $municipalities = array("北京", "上海", "天津", "重庆", "香港", "澳门");
        $sexes = array("", "男", "女");
        $data = array();
        $data['openid'] = $info['openid'];
        $data['nickname'] = str_replace("'", "", $info['nickname']);
        $data['sex'] = $sexes[$info['sex']];
        $data['country'] = $info['country'];
        $data['province'] = $info['province'];
        $data['city'] = (in_array($info['province'], $municipalities))?$info['province'] : $info['city'];
//        $data['scene'] = (isset($object->EventKey) && (stripos(strval($object->EventKey),"qrscene_")))?str_replace("qrscene_","",$object->EventKey):"0";

        $data['headimgurl'] = $info['headimgurl'];
        $data['subscribe'] = $info['subscribe_time'];
        $data['heartbeat'] = time();
        $data['remark'] = $info['remark'];
        $data['tagid'] = $info['tagid_list'];
        p($data);
        $content = "欢迎关注，".$info['nickname'];
        $userModel = new \app\index\model\WeixinUser();
        $userModel->edit($data);
        echo $userModel->getLastSql();
    }

    public function createMenuRaw()
    {
        $menu = '{
            "button":[
                {
                "type":"view",
                "name":"采购商城",
                "url":"https://hss.meishangyun.com/index/Index/index.html"
                },
                {
                "type":"view",
                "name":"加盟店",
                "url":"https://hss.meishangyun.com/index/Index/franchiseIndex.html"
                },
                {
                "type":"view",
                "name":"合伙人",
                "url":"https://hss.meishangyun.com/index/Index/cityPartnerIndex.html"
                }
           ]
        }';
        $mineTools = new \common\component\payment\weixin\Jssdk(config('wx_config.appid'), config('wx_config.appsecret'));
        $a =  $mineTools -> create_menu_raw($menu);
        p($a);
    }
}