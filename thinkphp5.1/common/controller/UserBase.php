<?php
namespace common\controller;

/**用户信息验证控制器基类
 */
class UserBase extends Base{
    protected $user = null;
    protected $loginUrl = 'index/UserCenter/login';//登录URL
    protected $indexUrl = 'Index/index';//登录URL

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
    //选择进入的店铺
    public function selectStore(){
        if(!request()->isPost()){
            return errorMsg('请求方式错误！');
        }
        $storeId = (int)input('post.store_id');
        session('currentStoreId',$storeId);
        return successMsg('成功！');
    }
}