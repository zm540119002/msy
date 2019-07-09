<?php
namespace app\index\controller;

// 前台首页
use think\Console;
use common\component\jwt\JWT;

class Test extends HssBase{
    /**首页
     */
    public function index(){
        $a =  strtolower(request()->module() . '/' . request()->controller() . '/' . request()->action());
        if(request()->isAjax()){
            return $_POST;
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
    /**测试-布局2
     */
    public function layout2(){
        if(request()->isAjax()){
        }else{
            return $this->fetch();
        }
    }
    /**测试-布局3
     */
    public function layout3(){
        if(request()->isAjax()){
        }else{
            return $this->fetch();
        }
    }
    public function signUp(){
        if(request()->isAjax()){
            $saveData['name'] = trim(input('post.name'));
            $saveData['mobile'] = trim(input('post.mobile'));
            $saveData['create_time'] = time();
            $model = new \app\index\model\SignUp();
            $id = $model -> edit($saveData);
            if($id){
                $this -> successMsg('报名预约成功');
            }else{
                $this -> errorMsg('失败');
            }
        }else{
            //微信分享
            $WeixinShareModel = new \app\index\model\WeixinShare();
            $shareInfo = $WeixinShareModel ->getShareInfo();
            $this->assign('shareInfo',$shareInfo);
            return $this->fetch();
        }
    }
    public function jin(){

        return $this->fetch();

    }
    public function position(){

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
        //p($city);
        //exit;
        $model = new \app\index\model\CityArea();

        $res = $model->getInfo($city);
        if(!$res){
            $res = $model->editAll($city);
        }

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


    public function getToken()
    {
        $key = "huang";  //这里是自定义的一个随机字串，应该写在config文件中的，解密时也会用，相当    于加密中常用的 盐  salt
        $token = [
            "iss" => "",  //签发者 可以为空
            "aud" => "", //面象的用户，可以为空
            "iat" => time(), //签发时间
            "nbf" => time() + 100, //在什么时候jwt开始生效  （这里表示生成100秒后才生效）
            "exp" => time() + 7200, //token 过期时间
            "uid" => 123 //记录的userid的信息，这里是自已添加上去的，如果有其它信息，可以再添加数组的键值对
        ];

        $jwt = JWT::encode($token, $key, "HS256"); //根据参数生成了 token
        return json([
            "token" => $jwt
        ]);
    }


    public function check()
    {
        $jwt = input("token");  //上一步中返回给用户的token
        $key = "huang";  //上一个方法中的 $key 本应该配置在 config文件中的
        //$info = JWT::decode($jwt, $key, ["HS256"]); //解密jwt
        try {
            $jwtAuth = json_encode(JWT::decode($jwt, $key, array('HS256')));
            $authInfo = json_decode($jwtAuth, true);
//            $jwtAuth = JWT::decode($jwt, $key, ["HS256"]);
//            $authInfo = json_decode($jwtAuth, true);
            p($authInfo);
            $msg = [];
            if (!empty($authInfo['uid'])) {
                $msg = [
                    'status' => 1001,
                    'msg' => 'Token验证通过'
                ];
            } else {
                $msg = [
                    'status' => 1002,
                    'msg' => 'Token验证不通过,用户不存在'
                ];
            }
            p($msg);
        } catch (\common\component\jwt\BeforeValidException $e) {
            return json_encode([
                'status' => 1002,
                'msg' => 'Token无效'
            ]);
        } catch (\common\component\jwt\ExpiredException $e) {
            return json_encode([
                'status' => 1003,
                'msg' => 'Token过期'
            ]);
        } catch (Exception $e) {
            return $e;
        }
    }


}