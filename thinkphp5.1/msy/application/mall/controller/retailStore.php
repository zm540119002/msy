<?php
namespace app\mall\controller;

class RetailStore extends MallBase{
    /**首页
     */
    public function index(){
        if(request()->isGet()){
            return $this->fetch();
        }
    }

    /**供应商零售店
     */
    public function goods(){
        if(request()->isGet()){
            $modelGoods = new \app\factory\model\Goods();
            $list = $modelGoods->getList();
        }
    }
}