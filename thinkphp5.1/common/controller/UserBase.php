<?php
namespace common\controller;

/**用户信息验证控制器基类
 */
class UserBase extends Base{
    protected $user = null;
    protected $loginUrl = 'ucenter/UserCenter/login';//登录页
    protected $indexUrl = 'index/Index/index';//平台首页
    
    public function __construct(){
        parent::__construct();
        //判断是否登录
        $this->user = checkLogin();
        if (!$this->user) {
            if(request()->isAjax()){
                $this->success('您还未登录平台，请先登录！',url($this->indexUrl),'no_login',0);
            }else{
//                $this->error('您还未登录平台，请先登录！',str_replace('/index.php','',url($this->loginUrl)));
                echo $this->fetch('api@template/loading.html');
                exit;
            }
        }
    }
}