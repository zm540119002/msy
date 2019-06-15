<?php
namespace app\index\controller;

class Company extends \common\controller\Base{
    /**首页
     */
    public function index(){

        return $this->fetch();
    }

    public function add(){



        //$this->successMsg('成功',config('code.success.default'));

        if(!request()->isPost()){
            return errorMsg('请求方式错误');
        }
        $postData = input('post.');
        p($postData);
        exit;
        $validate = new \app\index\validate\Franchise();
        if(!$validate->scene('add')->check($postData)) {
            return errorMsg($validate->getError());
        }


        $data['mobile_phone'] = trim($data['company_name']);
        $data['mobile_phone'] = trim($data['name']);
        $data['mobile_phone'] = trim($data['company_name']);
        $data['mobile_phone'] = trim($data['mobile_phone']);
        $saveData['salt'] = create_random_str(10,0);//盐值;
        $saveData['mobile_phone'] = $data['mobile_phone'];
        $saveData['password'] = md5($saveData['salt'] . $data['password']);
        $saveData['captcha'] = trim($data['captcha']);
        if(!$this->_checkCaptcha($saveData['mobile_phone'],$saveData['captcha'])){
            return errorMsg('验证码错误，请重新获取验证码！');
        }
        $user = $this->_registerCheck($saveData['mobile_phone']);
        $validateUser = new \common\validate\User();
        if(empty($user)){//未注册，则注册账号
            if(!$validateUser->scene('register')->check($data)) {
                return errorMsg($validateUser->getError());
            }
            if(!$this->_register($saveData)){
                return errorMsg('注册失败');
            }
        }elseif ($user['status'] ==1){
            return errorMsg('账号已禁用');
        }elseif ($user['status'] ==2){
            return errorMsg('账号已删除');
        }else{//已注册，正常，则修改密码
            if(!$validateUser->scene('resetPassword')->check($data)){
                return errorMsg($validateUser->getError());
            }
            if(!$this->_resetPassword($saveData['mobile_phone'],$saveData)){
                return errorMsg('重置密码失败');
            }
        }
        return $this->_login($saveData['mobile_phone'],$data['password']);

    }
}