<?php
namespace common\controller;

/**用户信息验证控制器基类
 */
class UserBase extends Base{
    protected $user = null;
    protected $loginUrl = 'index/UserCenter/login';//登录URL

    public function __construct(){
        parent::__construct();
        //判断是否登录
        $this->user = checkLogin();
        if (!$this->user) {
            if (request()->isAjax()) {
                header('HTTP/1.1 200');
                return successMsg('isAjax');
            }else{
                $this->error(config('custom.error_login'),url($this->loginUrl));
            }
        }
        \common\cache\Menu::removeAllMenuIds($this->user['id']);
        $allMenuIds = \common\cache\Menu::getAllMenuIds($this->user);
        $this->assign('allMenuIds',$allMenuIds);

        \common\cache\Menu::removeAllDisplayMenu($this->user['id']);
        $allDisplayMenu = \common\cache\Menu::getAllDisplayMenu($this->user);
        $this->assign('allDisplayMenu',$allDisplayMenu);
    }
}