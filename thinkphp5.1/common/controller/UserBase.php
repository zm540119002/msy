<?php
namespace common\controller;

/**用户信息验证控制器基类
 */
class UserBase extends Base{
    protected $user = null;
    protected $loginUrl = 'https://api.meishangyun.com/ucenter/UserCenter/login';//登录页
    protected $indexUrl = 'index/Index/index';//平台首页
    
    public function __construct(){
        parent::__construct();
        //判断是否登录
        $this->user = checkLogin();
        if (!$this->user) {
            if (request()->isAjax()) {
                $this->success('异步登录失败',url($this->indexUrl),'no_login',0);
            }else{
                $this->error(config('custom.error_login'),url($this->loginUrl));
            }
        }
    }
}