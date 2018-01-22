<?php
namespace AdminBusiness\Controller;

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
            return $this->redirect('User/login');
        }
    }
    
    /**
     * 判定是否登录
     * @return bool
     */
    public function isLogin() {
        //获取session
        $user = session('admin.user');
        if (($user && $user['id'])) {
            return true;
        }
        return false;

    }

 

}




