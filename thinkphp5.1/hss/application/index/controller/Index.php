<?php
namespace app\index\controller;
class Index extends \common\controller\Base{
    /**
     * 首页
     */
    public function index(){
        //获取精选的10个 场景
        $modelScene = new \app\index\model\Scene();
        $config =[
            'where' => [
                ['status', '=', 0],
                ['shelf_status','=',3],
                ['belong_to','exp','& 1'],
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