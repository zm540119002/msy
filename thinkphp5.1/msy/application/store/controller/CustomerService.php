<?php
namespace app\store\controller;

class CustomerService extends \common\controller\UserBase{
    /**首页
     */
    public function index(){
        if(!request()->isAjax()){
        }else{
            return $this->fetch();
        }
    }

    /**售前
     */
    public function beforeSale(){
        if(!request()->isAjax()){
        }else{
            return $this->fetch();
        }
    }

    /**售后
     */
    public function afterSale(){
        if(!request()->isAjax()){
        }else{
            return $this->fetch();
        }
    }
}