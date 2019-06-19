<?php
namespace app\index\controller;

// 前台首页
use think\Console;

class Test extends \common\controller\Base{
    /**首页
     */
    public function index(){
        if(request()->isAjax()){
        }else{
            return $this->fetch();
        }
    }
    /**测试
     */
    public function test(){
        if(request()->isAjax()){
        }else{
            return $this->fetch();
        }
    }
    /**测试-城市
     */
    public function city(){
        if(request()->isAjax()){
        }else{
            return $this->fetch();
        }
    }
    /**测试-城市2
     */
    public function city2(){
        if(request()->isAjax()){
        }else{
            return $this->fetch();
        }
    }
    /**测试-城市3
     */
    public function city3(){
        if(request()->isAjax()){
        }else{
            $unlockingFooterCart = unlockingFooterCartConfigTest([10,0,9]);
            array_push($unlockingFooterCart['menu'][0]['class'],'group_btn20');
            array_push($unlockingFooterCart['menu'][1]['class'],'group_btn40');
            array_push($unlockingFooterCart['menu'][2]['class'],'group_btn40');
            $this->assign('unlockingFooterCart',json_encode($unlockingFooterCart));
            return $this->fetch();
        }
    }
}