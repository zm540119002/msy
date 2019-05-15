<?php
namespace common\controller;

/**用户信息验证控制器基类
 */
class UserBase extends Base{
    protected $user = null;
    protected $indexUrl = 'index/Index/index';//平台首页
    
    public function __construct(){
        parent::__construct();
        //判断是否登录
        $this->user = checkLogin();
        if (!$this->user) {
            if(request()->isAjax()){
                $this->errorMsg(config('code.error.login.msg'),config('code.error.login'));
            }else{
                echo $this->fetch('../../api/public/template/login_page.html');
                exit;
            }
        }
    }
}