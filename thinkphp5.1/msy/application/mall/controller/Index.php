<?php
namespace app\mall\controller;

class Index extends MallBase{
    /**首页
     */
    public function index(){
        return $this->fetch();
    }

    /**供应商零售店
     */
    public function factoryRetailStore(){
        return $this->fetch();
    }

    /**美容机构门店
     */
    public function saleStore(){
        return $this->fetch();
    }
}