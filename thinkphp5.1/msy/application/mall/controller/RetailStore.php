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
            return view('list_tpl',['list'=>$list]);
        }else{
            return $this->fetch();
        }
    }

    /**商品详情页
     */
    public function detail(){
        if(request()->isAjax()){
        }else{
            $goodsId = intval(input('goodsId'));
            if($goodsId){
                $modelGoods = new \app\factory\model\Goods();
                $config =[
                    'where' => [
                        ['id', '=', $goodsId],
                    ],'field' => [
                        'g.id','g.name','g.sale_price','g.retail_price','g.main_img','g.parameters',
                    ],
                ];
                $info = $modelGoods->getInfo($config);
                if($info){
                    $info['main_img'] = explode(',',(string)$info['main_img']);
                    return $info;
                    $this->assign('info',$info);
                }
            }
            return $this->fetch();
        }
    }
}