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

            $unlockingFooterCart = unlockingFooterCartConfig([0, 2, 1]);
            $this->assign('unlockingFooterCart', $unlockingFooterCart);
            $this->assign('relation',config('custom.relation_type.promotion'));
        }

        return $this->fetch();
    }
}