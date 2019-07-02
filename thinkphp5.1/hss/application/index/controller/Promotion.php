<?php
namespace app\index\controller;
class Promotion extends HssBase{
    /**促销详情
     */
    public function detail(){
        if(!request()->isAjax()){
            if(!$id=input('id/d')) $this->error('此套餐已下架');
            // 促销信息
            $model = new\app\index\model\Promotion();
            $condition =[
                'field' => [
                    'id','name','main_img','tag','intro','title','retail_price',
                    'franchise_price','agent_price','remarks','share_title','share_desc'
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

            Index::getCartTotalNum();
            //$this->assignStandardBottomButton([0,2,3]);

            $unlockingFooterCart = unlockingFooterCartConfigTest([0,2,3]);
            array_push($unlockingFooterCart['menu'][0]['class'],'group_btn40');
            array_push($unlockingFooterCart['menu'][1]['class'],'group_btn20');
            array_push($unlockingFooterCart['menu'][2]['class'],'group_btn40');

            $this->assign('unlockingFooterCart',json_encode($unlockingFooterCart));

            $this->assign('relation',config('custom.relation_type.promotion'));
        }

        //微信分享
        $shareInfo = [
            'title'=>$info['share_title'], //分享的标题
            'shareLink'=>$this->host.$_SERVER['REQUEST_URI'], //分享的url
            'desc'=> $info['share_desc'], //分享的描述
            'shareImgUrl'=>$this->host.'/'.config('upload_dir.upload_path').'/'.$info['main_img'], //分享的图片
            'backUrl'=>$this->host.$_SERVER['REQUEST_URI'] //分享完成后跳转的url
        ];
        $this->assign('shareInfo',$shareInfo);
        return $this->fetch();
    }

    /**
     * 输出套餐列表信息
     */
    public function displayPromotionList($id,$type='scene'){
        if(!$id){
            return false;
        }
        switch($type){
            case 'sort' :
                $model = new \app\index\model\SortPromotion();
                $field_id = 'sp.sort_id';
                $join_id  = 'sp.promotion_id';
                break;
            case 'project' :
                $model = new \app\index\model\ProjectPromotion();
                //$condition['where'][] = ['pp.project_id','=',$id];
                $field_id = 'pp.project_id';
                $join_id  = 'pp.promotion_id';
                break;
            default;
                $model = new \app\index\model\ScenePromotion();
                //$condition['where'][] = ['sp.scene_id','=',$id];
                $field_id = 'sp.scene_id';
                $join_id  = 'sp.promotion_id';
        }

        $condition = [
            'where' => [
                ['p.status','=', 0], ['p.shelf_status','=', 3],
                [$field_id,'=',$id]
            ],'field'=>[
                'p.id','p.name','p.thumb_img'
            ],'join'=>[
                ['promotion p','p.id = '.$join_id,'left']
            ]
        ];

        $promotionList= $model->getList($condition);
        $modelPromotionGoods = new \app\index\model\PromotionGoods();

        $promotionList = $modelPromotionGoods->getListGoodsPrice($promotionList);

        $this->assign('promotionList',$promotionList);
    }

}