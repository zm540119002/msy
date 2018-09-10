<?php
namespace app\purchase\controller;

class Store extends MallBase{
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
    public function goodsList(){
        $modelGoods = new \app\factory\model\Goods();
        $config =[
            'where' => [
                ['g.status', '=', 0],
                ['s.status', '=', 0],
//                ['s.run_type', '=', 2],
            ],'field' => [
                'g.id','g.name','g.thumb_img','g.sale_price','s.id as sid'
            ],'leftJoin' => [
                ['store s','g.store_id = s.id',],
            ],
        ];
        $list = $modelGoods->pageQuery($config);
        print_r($list);exit;
        return view('list_tpl',['list'=>$list]);
       if(request()->isAjax()){
            $modelGoods = new \app\factory\model\Goods();
            $config =[
                'where' => [
                    ['g.status', '=', 0],
                    ['s.status', '=', 0],
                    ['s.run_type', '=', 2],
                ],'field' => [
                     'g.id','g.name','g.thumb_img','g.sale_price','s.id as sid'
                ],'leftJoin' => [
                    ['store s','g.store_id = s.id',],
                ],
            ];
            $list = $modelGoods->pageQuery($config);
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
                        ['g.status', '=', 0],
                        ['g.id', '=', $goodsId],
                    ],'field' => [
                        'g.id','g.name','g.sale_price','g.retail_price','g.main_img','g.parameters',
                    ],
                ];
                $info = $modelGoods->getInfo($config);
                if($info){
                    $info['main_img'] = explode(',',(string)$info['main_img']);
                    $this->assign('info',$info);
                }
            }
            return $this->fetch();
        }
    }
}