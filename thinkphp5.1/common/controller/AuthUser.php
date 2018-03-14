<?php
namespace common\controller;

/**登录验证控制器基类
 */
class AuthUser extends Base{
    protected $user = null;
    protected $loginUrl = 'Home/User/login';//登录URL

    public function __construct(){
        parent::__construct();
        //判断是否登录
        $this->user = AuthUser::check();
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