<?php
namespace web\all\Controller;

use web\all\Lib\AuthUser;

/**登录验证控制器基类
 */
class AuthUserController extends BaseController{
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
//        $auth = array(
//            'id' => 16,
//            'mobile' => '18664368697',
//        );
//        session('user', $auth);
    }
}