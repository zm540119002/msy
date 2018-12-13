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
                ['gc.status', '=', 0],
                ['gc.level','=',1]
            ], 'order'=>[
                'sort'=>'desc',
                'id'=>'desc'
            ],  'limit'=>'5'

        ];
        $categoryList  = $modelGoodsCategory->getList($config);
        $this ->assign('categoryList',$categoryList);
        //获取精选的6个项目
        $modelProject = new \app\weiya_customization\model\Project();
        $config =[
            'where' => [
                ['p.status', '=', 0],
                ['p.is_selection', '=', 1],
                ['p.shelf_status','=',3]
            ], 'order'=>[
                'sort'=>'desc',
                'id'=>'desc'
            ],  'limit'=>'6'

        ];
        $projectList  = $modelProject->getList($config);
        $this ->assign('projectList',$projectList);
        return $this->fetch();
    }
}