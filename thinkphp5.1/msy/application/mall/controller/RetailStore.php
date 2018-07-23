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
                    'g.id','g.name','g.thumb_img','g.sale_price',
                ],
            ];
            $list = $modelGoods->getList($config);
//            $this->assign('list',$list);
            return view('list_tpl',['list'=>$list]);
        }else{
            return $this->fetch();
        }
    }
}