<?php
namespace app\index\controller;
class Studio extends \common\controller\Base{
    /**
     * 获取工作室的场景&&商品
     */
    public function index(){

        // 促销列表 7个
        $modelPromotion = new \app\index\model\Promotion();
        $condition =[
            'where' => [
                ['status', '=', 0],
                ['shelf_status','=',3],
                ['thumb_img','<>',''],
            ],
            'field'=>['id','name','thumb_img'],
            'order'=>['sort'=>'desc', 'id'=>'desc',],
            'limit'=>'7'
        ];
        $promotionList  = $modelPromotion->getList($condition);
        $this ->assign('promotionList',$promotionList);

        //获取设置的11个 场景
        $modelScene = new \app\index\model\Scene();
        $config =[
            'where' => [
                ['status', '=', 0],
                ['shelf_status','=',3],
                //['is_selection','=',1],
                ['belong_to','exp','& 2'],
            ],
            'field'=> ['id','name','row_number','thumb_img','template'],
            'order'=>[
                'sort'=>'desc',
                'id'=>'desc'
            ],  'limit'=>'11'

        ];
        $sceneList  = $modelScene->getList($config);
        // 场景按行个数分组
        $sceneLists = sceneRatingList($sceneList);
        $this ->assign('sceneLists',$sceneLists);

        // 底部菜单，见配置文件custom.footer_menu
        $this->assign('currentPage',request()->controller().'/'.request()->action());
        return $this->fetch();
    }
}