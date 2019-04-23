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
                $this->success('您还未登录平台，请先登录！',url($this->indexUrl),'no_login',0);
            }else{
                echo $this->fetch('../../api/public/template/login_page.html');
                exit;
            }
        }
    }
}