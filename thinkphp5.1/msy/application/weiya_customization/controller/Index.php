<?php
namespace app\weiya_customization\controller;

class Index extends \common\controller\Base{
    /**首页
     */
    public function index(){
        //获取商品的分类
        $modelGoodsCategory = new \app\weiya_customization\model\GoodsCategory();
        $config =[
            'where' => [
                ['status', '=', 0],
                ['level','=',1]
            ], 'order'=>[
                'sort'=>'desc',
                'id'=>'desc'
            ],  'limit'=>'5'

        ];
        $categoryList  = $modelGoodsCategory->getList($config);
        $this ->assign('categoryList',$categoryList);

        //获取精选的6个 场景
        $modelScene = new \app\weiya_customization\model\Scene();
        $config =[
            'where' => [
                ['status', '=', 0],
                ['is_selection', '=', 1],
                ['shelf_status','=',3]
            ], 'order'=>[
                'sort'=>'desc',
                'id'=>'desc'
            ],  'limit'=>'6'

        ];
        $sceneList  = $modelScene->getList($config);
        $this ->assign('sceneList',$sceneList);

        //获取精选的10个项目
        $modelProject = new \app\weiya_customization\model\Project();
        $config =[
            'where' => [
                ['status', '=', 0],
                ['is_selection', '=', 1],
                ['shelf_status','=',3]
            ], 'order'=>[
                'sort'=>'desc',
                'id'=>'desc'
            ],  'limit'=>'6'

        ];
        $projectList  = $modelProject->getList($config);
        $this ->assign('projectList',$projectList);

        //获取精选的6个项目
        $modelGoods = new \app\weiya_customization\model\Goods();
        $config =[
            'where' => [
                ['status', '=', 0],
                ['is_selection', '=', 1],
                ['shelf_status','=',3]
            ], 'order'=>[
                'sort'=>'desc',
                'id'=>'desc'
            ],  'limit'=>'6'

        ];
        $goodsList  = $modelGoods->getList($config);
        $this ->assign('goodsList',$goodsList);
        return $this->fetch();
    }

    
}