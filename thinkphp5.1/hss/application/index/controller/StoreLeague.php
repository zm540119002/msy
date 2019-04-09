<?php
namespace app\index\controller;
class StoreLeague extends \common\controller\Base{

    public function __construct(){
        parent::__construct();
    }

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