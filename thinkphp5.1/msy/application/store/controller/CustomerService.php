<?php
namespace app\store\controller;

class CustomerService extends \common\controller\UserBase{
    /**首页
     */
    public function index(){
        if(!request()->isAjax()){
            return successMsg(123);
        }else{
            return $this->fetch();
        }
    }

    /**售前
     */
    public function beforeSale(){
        if(!request()->isAjax()){
            return successMsg(123);
        }else{
            return $this->fetch();
        }
    }

    /**售后
     */
    public function afterSale(){
        if(!request()->isAjax()){
            return successMsg(123);
        }else{
            return $this->fetch();
        }
    }
}