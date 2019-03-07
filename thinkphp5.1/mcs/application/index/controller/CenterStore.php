<?php
namespace app\index\controller;
class CenterStore extends \common\controller\Base{
    /**首页
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

    /**
     * 默认二级场景页
     * 需要同组的各场景的名，场景信息，场景下的商品，场景下的活动
     */
    public function detail(){
        if(request()->isAjax()){
        }else{
            $id = intval(input('id'));
            if(!$id){
                $this->error('此项目已下架');
            }
            // 场景信息
            $model = new\app\index\model\Scene();
            $config =[
                'where' => [
                    ['status', '=', 0],
                    ['shelf_status', '=', 3],
                    ['id', '=', $id],
                ],
            ];
            $css = (input('css'));
            $this->assign('css',$css);
            $scene = $model->getInfo($config);
            if(empty($scene)){
                $this->error('此项目已下架');
            }
            $scene['main_img'] = explode(',',(string)$scene['main_img']);
            $scene['tag'] = explode(',',(string)$scene['tag']);
            $this->assign('scene',$scene);

            // 同组的各场景的名
            $config = [
                'where' => [
                    ['group','=',$scene['group']],
                ],
                'order' => [
                    'sort' => 'desc',
                ],
            ];
            $sceneList = $model->getList($config);
            $this->assign('sceneList',$sceneList);

            //获取相关的商品
            $modelSceneGoods = new \app\index\model\SceneGoods();
            $config = [
                'where' => [
                    ['sg.status', '=', 0],
                    ['sg.scene_id', '=', $id],
                ],'field'=>[
                    'g.id ','g.headline','g.thumb_img','g.bulk_price','g.specification','g.minimum_order_quantity',
                    'g.minimum_sample_quantity','g.increase_quantity','g.purchase_unit'
                ],'join'=>[
                    ['goods g','g.id = sg.goods_id','left']
                ]
            ];

            $goodsList= $modelSceneGoods->getList($config);

            $this->assign('goodsList',$goodsList);
            $unlockingFooterCart = unlockingFooterCartConfig([0,2,1]);
            $this->assign('unlockingFooterCart', $unlockingFooterCart);

            return $this->fetch();
        }
    }
}