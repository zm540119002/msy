<?php
namespace app\index\controller;
class Business extends \common\controller\UserBase {

    /**首页
     */
    public function index(){
        if(request()->isAjax()){
        }else{
            // 底部菜单，见配置文件custom.footer_menu
            $this->assign('currentPage',request()->controller().'/'.request()->action());
            
            return $this->fetch();
        }
    }
}