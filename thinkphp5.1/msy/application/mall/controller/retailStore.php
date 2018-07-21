<?php
namespace app\mall\controller;

class RetailStore extends MallBase{
    /**首页
     */
    public function index(){
        if(request()->isAjax()){
        }else{
            return $this->fetch();
        }
    }

    /**供应商零售店
     */
    public function goods(){
        if(request()->isAjax()){
            $modelGoods = new \app\factory\model\Goods();
            $config =[
                'field' => [
                    'g.id','g.name',
                ],
            ];
            $list = $modelGoods->getList($config,'g');
            return $list;
        }else{
            return $this->fetch();
        }
    }
}