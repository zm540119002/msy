<?php
namespace app\index_admin\controller;

class Order extends Base{
    //首页
    public function manage(){

        return $this->fetch();
    }

    /**
     *  分页查询
     */
    public function getList(){

        $model = new \app\index_admin\model\Goods();

        $where[] = ['g.status','=',0];

        if($category_id_1 = input('category_id_1/d')){
            $where[] = ['g.category_id_1','=',$category_id_1];
        }
        if($category_id_2 = input('category_id_2/d')){
            $where[] = ['g.category_id_2','=',$category_id_2];
        }
        if($category_id_3 = input('category_id_3/d')){
            $where[] = ['g.category_id_3','=',$category_id_3];
        }
        if($belong_to = input('belong_to/d')){ // 经过特别处理
            $where[] = ['g.belong_to','=',$belong_to];
        }
        if($shelf_status = input('shelf_status/d')){
            $where[] = ['g.shelf_status','=',$shelf_status];
        }

        $keyword = input('get.keyword','','string');
        if($keyword){
            $where[] = ['g.name','like', '%' . trim($keyword) . '%'];
        }
        $config = [
            'where'=>$where,
            'field'=>[
                'g.id','g.name','g.bulk_price','g.sample_price','g.sort','g.is_selection',
                'g.thumb_img','g.shelf_status','g.create_time','g.rq_code_url','g.belong_to'
//                'g.category_id_1',
//                'gc1.name as category_name_1'
            ],
//            'join' => [
//                ['goods_category gc1','gc1.id = g.category_id_1'],
//            ],
            'order'=>[
                'g.sort'=>'desc',
                'g.id'=>'desc',
            ],
        ];

        $list = $model ->pageQuery($config);

        $this->assign('list',$list);
        if($_GET['pageType'] == 'layer'){
            return view('goods/list_layer_tpl');
        }
        if($_GET['pageType'] == 'manage'){
            return view('goods/list_tpl');
        }
    }
}