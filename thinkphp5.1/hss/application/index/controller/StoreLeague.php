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
            return $this->fetch();
        }
    }
}