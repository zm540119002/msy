<?php
namespace common\controller;

use common\lib\User;

/**用户信息验证控制器基类
 */
class AuthUser extends WXBase{
    protected $user = null;
    protected $loginUrl = 'index/User/login';//登录URL

    public function __construct(){
        parent::__construct();
        //判断是否登录
        $this->user = User::check();
        if (!$this->user) {
            if (IS_AJAX) {
                header('HTTP/1.1 200');
                $this->ajaxReturn(successMsg('isAjax'));
            }else{
                $this->error(C('ERROR_LOGIN_REMIND'),U($this->loginUrl));
            }
        }
    }
}