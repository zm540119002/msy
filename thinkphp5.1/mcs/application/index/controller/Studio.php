<?php
namespace app\index\controller;
class Studio extends \common\controller\Base{
    /**
     * 获取工作室的场景&&商品
     */
    public function index(){
        //获取商品的分类
        $modelGoodsCategory = new \app\index\model\GoodsCategory();
        $config =[
            'where' => [
                ['status', '=', 0],
                ['level','=',1]
            ], 'order'=>[
                'sort'=>'desc',
                'id'=>'desc'
            ],  'limit'=>'7'
        ];
        $categoryList  = $modelGoodsCategory->getList($config);
        $this ->assign('categoryList',$categoryList);
        //获取精选的10个 场景
        $modelScene = new \app\index\model\Scene();
        $config =[
            'where' => [
                ['status', '=', 0],
                ['shelf_status','=',3],
                //['is_selection','=',1],
                ['belong_to','exp','& 2'],

            ], 'order'=>[
                'sort'=>'desc',
                'id'=>'desc'
            ],  'limit'=>'11'

        ];
        $sceneList  = $modelScene->getList($config);
        // 场景按行个数分组
        $sceneLists = sceneRatingList($sceneList);

        $this ->assign('sceneLists',$sceneLists);

        //获取精选的10个项目
        $modelProject = new \app\index\model\Project();
        $config =[
            'where' => [
                ['status', '=', 0],
                ['shelf_status','=',3],
                ['is_selection','=',1],

            ], 'order'=>[
                'sort'=>'desc',
                'id'=>'desc'
            ],  'limit'=>'11'
        ];
        $projectList  = $modelProject->getList($config);
        $this ->assign('projectList',$projectList);
        return $this->fetch();
    }
}