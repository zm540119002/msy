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

            promotion_handle($info);
            $this->assign('info',$info);



            // 购物车商品总数量
            $cartSum = 0;
            if( $user = session('user') ){
                $modelCart = new \app\index\model\Cart();
                $cartSum  = $modelCart->where([['user_id','=',$user['id']],['status','=',0]])->sum('num');
            }

            $this->assign('cartSum',$cartSum);

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


}