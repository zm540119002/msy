<?php
namespace app\index\controller;

// 前台首页
class Index extends \common\controller\Base{
    /**
     * 促销列表，场景列表，商品列表 -ajax
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

        //获取精选的10个 场景
        $modelScene = new \app\index\model\Scene();
        $condition =[
            'where' => [
                ['status', '=', 0],
                ['shelf_status','=',3],
            ],
            'field'=>['id','name','thumb_img','template','row_number'],
            'order'=>['row_number'=>'desc', 'sort'=>'desc', 'id'=>'desc',],
            'limit'=>'11'

        ];
        $sceneList  = $modelScene->getList($condition);

        // 场景按行个数分组
        $sceneLists = sceneRatingList($sceneList);
        $this ->assign('sceneLists',$sceneLists);

        // 底部菜单
        $footer_menu = config('custom.footer_menu');
        $footer_menu[1]['class'] = 'current';
        $this->assign('footer_menu',$footer_menu);

        return $this->fetch();
    }

    public function test(){
        echo basename();
        if(request()->isAjax()){
        }else{
            return $this->fetch();
        }
    }
}