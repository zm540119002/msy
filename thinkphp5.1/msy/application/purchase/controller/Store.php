<?php
namespace app\purchase\controller;

class Store extends Base{
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
       if(request()->isAjax()){

        }else{
           $storeId = input('param.store_id','int');
//           if(!positiveInteger($storeId)){
//               return 111;
//           }
            $this->assign('storeId',$storeId);
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