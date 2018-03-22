<?php
namespace common\controller;

use common\lib\UserAuth;

/**用户信息验证控制器基类
 */
class UserBase extends Base{
    protected $user = null;
    protected $loginUrl = 'index/User/login';//登录URL

    public function __construct(){
        parent::__construct();
        //判断是否登录
        $this->user = UserAuth::check();
        if (!$this->user) {
            if (request()->isAjax()) {
                header('HTTP/1.1 200');
                $this->ajaxReturn(successMsg('isAjax'));
            }else{
                $this->error(config('custom.error_login'),url($this->loginUrl));
            }
        }
    }
}