<?php
namespace app\index\controller;
class Index extends \common\controller\Base{
    /**首页
     */
    public function index(){

        // 还没有做好，暂时不显示
        $this->redirect('CenterStore/index');
        exit;

        // 商品
        $model = new \app\index\model\Goods();

        $goods = $model->getList();
        $this->assign('goods',$goods);


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
                ['is_selection','=',1],
            ], 'order'=>[
                'sort'=>'desc',
                'id'=>'desc'
            ],  'limit'=>'11'
        ];
        $sceneList  = $modelScene->getList($config);
        $this ->assign('sceneList',$sceneList);

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

        // 底部菜单，见配置文件custom.footer_menu
        $this->assign('currentPage',request()->controller().'/'.request()->action());

        return $this->fetch();
    }
}