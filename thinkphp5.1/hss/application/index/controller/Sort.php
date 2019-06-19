<?php
namespace app\index\controller;

/**
 * 品类控制器
 * Class Sort
 * @package app\index\controller
 */

class Sort extends \common\controller\Base{
    /**首页
     */
    public function index(){
        if(request()->isAjax()){
        }else{
            return $this->fetch();
        }
    }

    /**
     * 列表
     */
    public function getList(){
        if(!request()->isGet()){
            return errorMsg('参数有误');
        }

        $model = new \app\index\model\Sort();
        $condition = [
            'field' => [
                'id','name','thumb_img','intro'
            ],
            'where' => [
                ['status','=',0],
                ['shelf_status','=',3],
            ],'order' => ['sort desc']
        ];

        $list = $model -> pageQuery($condition);
        $this->assign('list',$list);


        return $this->fetch('list_tpl');
    }


    /**
     * 详情页
     */
    public function detail(){
        if(request()->isAjax()){
        }else{

            $id = input('id/d');
            if(!$id){
                $this->error('此项目已下架');
            }
            $model = new \app\index\model\Sort();
            $config =[
                'field' => [
                    'id','name','main_img','intro','tag','detail_img','title','process_img','recommend_goods'
                ],
                'where' => [
                    ['status', '=', 0],
                    ['shelf_status', '=', 3],
                    ['id', '=', $id],
                ],
            ];
            $info = $model->getInfo($config);
            if(empty($info)){
                $this->error('此项目已下架');
            }

            // 品类推荐商品
            $modelGoods = new \app\index\model\Goods();
            $condition = [
                'field' => [
                    'id','name','specification','thumb_img'
                ],'where' => [
                    ['status','=',0],
                    ['shelf_status','=',3],
                    ['id','in',$info['recommend_goods']],
                ]
            ];
            $info['recommend_goods'] = $modelGoods->getList($condition);

            sort_handle($info);

            $this->assign('info',$info);

            Promotion::displayPromotionList($id,'sort');

            $this->assign('relation',config('custom.relation_type.sort'));
            //$this->assign('relation',config('custom.relation_type.project'));
/*            $unlockingFooterCart = unlockingFooterCartConfigTest([0,2,1,3]);
            array_push($unlockingFooterCart['menu'][0]['class'],'group_btn30');
            array_push($unlockingFooterCart['menu'][1]['class'],'group_btn20');
            array_push($unlockingFooterCart['menu'][2]['class'],'group_btn25');
            array_push($unlockingFooterCart['menu'][3]['class'],'group_btn25');
            $this->assign('unlockingFooterCart',json_encode($unlockingFooterCart));
            Cart::getCartTotalNum();*/

            foot_cart_menu();
            Cart::getCartTotalNum();

            return $this->fetch();
        }
    }

    /**详情页 old
     */
    public function detailImg(){
        if(request()->isAjax()){
        }else{
            $id = intval(input('id'));
            if(!$id){
                $this->error('此项目已下架');
            }
            $model = new \app\index\model\Sort();
            $config =[
                'where' => [
                    ['p.status', '=', 0],
                    ['p.shelf_status', '=', 3],
                    ['p.id', '=', $id],
                ],
            ];
            $info = $model->getInfo($config);
            if(empty($info)){
                $this->error('此商品已下架');
            }
            $info['main_img'] = explode(',',(string)$info['main_img']);
            $info['tag'] = explode(',',(string)$info['tag']);
            $this->assign('info',$info);

            //获取相关的商品
            $modelSortGoods = new \app\index\model\SortGoods();
            $config =[
                'where' => [
                    ['pg.status', '=', 0],
                    ['pg.sort_id', '=', $id],
                ],'field'=>[
                    'g.id ','g.headline','g.thumb_img','g.franchise_price','g.specification','g.minimum_order_quantity',
                    'g.minimum_sample_quantity','g.increase_quantity','g.purchase_unit'
                ],'join'=>[
                    ['goods g','g.id = pg.goods_id','left']
                ]
            ];
            $goodsList= $modelSortGoods->getList($config);
            $this->assign('goodsList',$goodsList);
            $unlockingFooterCart = unlockingFooterCartConfig([0,2,1]);
            $this->assign('unlockingFooterCart', $unlockingFooterCart);
            return $this->fetch();
        }
    }

}