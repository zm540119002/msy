<?php
namespace app\index\controller;

/**
 * 促销控制器
 */
class Promotion extends \common\controller\Base{

    /**
     * 促销详情
     */
    public function detail(){

        if(!request()->isAjax()){

            if(!$id=input('id/d')) $this->error('此套餐已下架');

            // 促销信息
            $model = new\app\index\model\Promotion();
            $condition =[
                'field' => [
                    'id','name','main_img','tag','intro','title'
                ], 'where' => [
                    ['status', '=', 0],
                    ['shelf_status', '=', 3],
                    ['id', '=', $id],
                ]
            ];

            $info = $model->getInfo($condition);

            if(empty($info)){
                $this->error('此套餐已下架');
            }

            $info['tag'] = explode('|',(string)$info['tag']);
            $info['main_img'] = explode(',',(string)$info['main_img']);
            $info['intro'] = $info['intro'] ? htmlspecialchars_decode($info['intro']) : $info['intro'] ;

            $this->assign('info',$info);

            $unlockingFooterCart = unlockingFooterCartConfigTest([0,2,1,3]);
            array_push($unlockingFooterCart['menu'][0]['class'],'group_btn30');
            array_push($unlockingFooterCart['menu'][1]['class'],'group_btn30');
            array_push($unlockingFooterCart['menu'][2]['class'],'group_btn30');
            array_push($unlockingFooterCart['menu'][3]['class'],'group_btn30');
            $this->assign('unlockingFooterCart',json_encode($unlockingFooterCart));

            $this->assign('relation',config('custom.relation_type.promotion'));
        }

        return $this->fetch();
    }

    /**
     * 获取各套餐列表商品总价
     */
    public function getListGoodsPrice($list){

        $modelPromotionGoods = new \app\index\model\PromotionGoods();
        // 套餐下的商品总价 单个
        foreach($list as $k => $v){

            if( $v['id']>0 ){

                $condition = [
                    'field' => [
                        'sum(g.bulk_price) price',
                    ], 'where' => [
                        ['g.status','=',0],
                        ['g.shelf_status','=',3],
                        ['pg.promotion_id','=',$v['id']],
                    ],'join' => [
                        ['goods g','pg.goods_id = g.id','left']
                    ],
                ];

                $info = $modelPromotionGoods->getInfo($condition);
                $list[$k]['price'] = $info['price'];
            }
        }
        return $list;
    }


}