<?php
namespace Admin\Controller;

use web\all\Controller\BaseController;

/**
 * 需要验证登录信息的都继承此基类
 */
class BaseAuthUserController extends BaseController{
    public function __construct(){
        parent::__construct();
        // 判定用户是否登录
        $isLogin = $this->isLogin();
        if(!$isLogin) {
            return $this->redirect('login/index');
        }
    }



    /**
     * 判定是否登录
     * @return bool
     */
    public function isLogin() {
        //获取session
        $user = session(config('admin.session_user'), '', config('admin.session_user_scope'));
        if($user && $user->id) {
            return true;
        }

        return false;
    }
}


