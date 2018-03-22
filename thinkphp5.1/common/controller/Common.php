<?php
namespace common\controller;

use think\Controller;

/**用户信息验证控制器基类
 */
class Common extends Controller{
    public function __construct(){
        parent::__construct();
        //登录验证后跳转回原验证发起页
        $this->host = isset($_SERVER['HTTP_X_FORWARDED_HOST']) ? $_SERVER['HTTP_X_FORWARDED_HOST'] :
            (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '');
        session('backUrl',$_SERVER['REQUEST_URI'] ? $this->host . $_SERVER['REQUEST_URI'] : $this->host . $_SERVER['HTTP_REFERER']);
        //多步跳转后回原发起页
        session('returnUrl',input('get.returnUrl','','string')?:input('post.returnUrl','','string'));
    }
}