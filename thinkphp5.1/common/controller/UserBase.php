<?php
namespace common\controller;

/**用户信息验证控制器基类
 */
class UserBase extends Base{
    protected $user = null;
    protected $loginUrl = 'index/UserCenter/login';//登录URL
<<<<<<< HEAD
    protected $indexUrl = 'Index/index';//登录URL
=======
    protected $indexUrl = 'store/Index/index';//采购商首页URL
>>>>>>> fef131517042a2a28672bb56ce5b1c7c8028cdff

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