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

            if(!$id=input('id/d')) $this->error('此促销已下架');

            // 促销信息
            $model = new\app\index\model\Promotion();
            $condition = [
                'where' => [['status', '=', 0], ['shelf_status', '=', 3], ['id', '=', $id]],
                'field' => ['id','name','main_img','intro','background_img','logo_img']
            ];
            $css = (input('css'));
            $this->assign('css', $css);
            $info = $model->getInfo($condition);
            if (empty($info)) {
                $this->error('此项目已下架');
            }
            $info['main_img'] = explode(',', (string)$info['main_img']);
            $this->assign('info', $info);

            $unlockingFooterCart = unlockingFooterCartConfig([0, 2, 1]);
            $this->assign('unlockingFooterCart', $unlockingFooterCart);
            $this->assign('relation',config('custom.relation_type.promotion'));
        }

        return $this->fetch();
    }
}