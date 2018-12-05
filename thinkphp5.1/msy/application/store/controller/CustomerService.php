<?php
namespace app\store\controller;

class CustomerService extends \common\controller\UserBase{
    /**首页
     */
    public function index(){
        return $this->fetch();
    }

    /**售前
     */
    public function beforeSale(){
        return $this->fetch('index');
    }

    /**售后
     */
    public function afterSale(){
        return $this->fetch('index');
    }
}