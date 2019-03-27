<?php
namespace app\index\controller;

// 前台首页
class Index extends \common\controller\Base{
    /**
     * 场景列表，商品列表 -ajax
     */
    public function index(){
        //获取精选的10个 场景
        $modelScene = new \app\index\model\Scene();
        $config =[
            'where' => [
                ['status', '=', 0],
                ['shelf_status','=',3],
            ], 'order'=>[
                'row_number'=>'desc',
                'sort'=>'desc',
                'id'=>'desc',
            ],  'limit'=>'11'

        ];
        $sceneList  = $modelScene->getList($config);

        // 场景按行个数分组
        $sceneLists = sceneRatingList($sceneList);

        $this ->assign('sceneLists',$sceneLists);

        return $this->fetch();
    }
}