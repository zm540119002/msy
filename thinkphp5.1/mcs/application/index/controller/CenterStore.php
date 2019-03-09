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
     * 二级场景页 -场景(默认)
     * 需要同组的各场景的名，场景信息，场景下的商品，场景下的方案
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

            // 获取场景下的方案
            $modelSceneScheme = new \app\index\model\SceneScheme();
            $config = [
                'where' => [
                    ['ss.status', '=', 0],
                    ['ss.scene_id', '=', $id],
                ],'field'=>[
                    '*'
                ],'join'=>[
                    ['scheme s','s.id = ss.scheme_id','left']
                ]
            ];

            $schemeList= $modelSceneScheme->getList($config);
            //p($schemeList);die;
            $this->assign('schemeList',$schemeList);

            $unlockingFooterCart = unlockingFooterCartConfig([0,2,1]);
            $this->assign('unlockingFooterCart', $unlockingFooterCart);

            return $this->fetch();
        }
    }

    /**
     * 二级场景页 -分类
     * 需要场景信息，场景下的商品分类，商品分类下的商品
     */
    public function sort(){
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

            // 场景下的商品分类
            $modelSceneGoodsCategory = new \app\index\model\SceneGoodsCategory();
            $config = [
                'where'  => [
                    ['gc.status', '=', 0],
                    ['sgc.scene_id', '=', $id],
                ],'field'=> [
                    'gc.id ','gc.name',
                ],'join' => [
                    ['goods_category gc','gc.id = sgc.goods_category_id','left']
                ],'order'=> [
                    'gc.sort'=>'desc'
                ]
            ];
            $categoryList = $modelSceneGoodsCategory->getList($config);
            $this->assign('categoryList',$categoryList);

            // 场景下的首商品分类的商品
            $goodsList = array();
            if($categoryList){
                $category = reset($categoryList);
                $modelGoods = new \app\index\model\Goods();
                $config = [
                    'where' => [
                        ['status', '=', 0],
                        ['category_id_1', '=', $category['id']],
                    ],'field'=>[
                        'id ','headline','thumb_img','bulk_price','specification','minimum_order_quantity',
                        'minimum_sample_quantity','increase_quantity','purchase_unit'
                    ],'order'=> [
                        'sort' => 'desc'
                    ]
                ];
                $goodsList= $modelGoods->getList($config);
            }

            $this->assign('goodsList',$goodsList);

            $unlockingFooterCart = unlockingFooterCartConfig([0,2,1]);
            $this->assign('unlockingFooterCart', $unlockingFooterCart);

            return $this->fetch();
        }
    }

    /**
     * 二级场景页 -项目
     * 需要场景信息，场景下的项目信息(商品，介绍，视频)
     */
    public function project(){
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

            // 场景下的商品分类
            $modelSceneGoodsCategory = new \app\index\model\SceneGoodsCategory();
            $config = [
                'where'  => [
                    ['gc.status', '=', 0],
                    ['sgc.scene_id', '=', $id],
                ],'field'=> [
                    'gc.id ','gc.name',
                ],'join' => [
                    ['goods_category gc','gc.id = sgc.goods_category_id','left']
                ],'order'=> [
                    'gc.sort'=>'desc'
                ]
            ];
            $categoryList = $modelSceneGoodsCategory->getList($config);
            $this->assign('categoryList',$categoryList);

            $category = reset($categoryList);

            // 场景下的首商品分类的商品
            $modelGoods = new \app\index\model\Goods();
            $config = [
                'where' => [
                    ['status', '=', 0],
                    ['category_id_1', '=', $category['id']],
                ],'field'=>[
                    'id ','headline','thumb_img','bulk_price','specification','minimum_order_quantity',
                    'minimum_sample_quantity','increase_quantity','purchase_unit'
                ],'order'=> [
                    'sort'=>'desc'
                ]
            ];

            $goodsList= $modelGoods->getList($config);
            $this->assign('goodsList',$goodsList);

            $unlockingFooterCart = unlockingFooterCartConfig([0,2,1]);
            $this->assign('unlockingFooterCart', $unlockingFooterCart);

            return $this->fetch();
        }
    }
}