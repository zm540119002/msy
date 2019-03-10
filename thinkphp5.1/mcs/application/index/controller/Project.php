<?php
namespace app\index\controller;

class Project extends \common\controller\Base{
    /**首页
     */
    public function index(){
        if(request()->isAjax()){
        }else{
            return $this->fetch();
        }
    }

    /**
     * 查询项目下的商品 分页查询
     */
    public function getGoodsList(){
        if(!request()->isGet()){
            return errorMsg('请求方式错误');
        }
        $model = new \app\index\model\ProjectGoods();
        $config=[
            'where'=>[
                ['pg.status','=',0]
            ],
            'field'=>[
                'g.*'
            ],
            'join'=>[
                ['goods g','g.id = pg.goods_id','left']
            ],
            'order'=>[
                'sort'=>'desc',
            ],
        ];
        if(input('?get.storeId') && (int)input('?get.storeId')){
            $config['where'][] = ['g.store_id', '=', input('get.storeId')];
        }
        if(input('?get.belong_to') && (int)input('?get.belong_to')){
            $config['where'][] = ['g.belong_to', '=', input('get.belong_to')];
        }
        if(input('?get.project_id') && (int)input('?get.project_id')){
            $config['where'][] = ['pg.project_id', '=', input('get.project_id')];
        }

        $keyword = input('get.keyword','');
        if($keyword) {
            $config['where'][] = ['name', 'like', '%' . trim($keyword) . '%'];
        }
        $list = $model -> pageQuery($config);

        $this->assign('list',$list);
        if(isset($_GET['pageType'])){
            // 排列的数量不同
            switch($_GET['pageType']){
                case 'column_1' : return $this->fetch('list_column_1_tpl'); break;   // 一行一个
            }
        }
    }

    /**详情页
     */
    public function detail(){
        if(request()->isAjax()){
        }else{
            $id = intval(input('id'));
            if(!$id){
                $this->error('此项目已下架');
            }
            $model = new \app\index\model\Project();
            $config =[
                'where' => [
                    ['p.status', '=', 0],
                    ['p.shelf_status', '=', 3],
                    ['p.id', '=', $id],
                ],
            ];
            $info = $model->getInfo($config);
            if(empty($info)){
                $this->error('此商品已下架');
            }
            $info['detail_img'] = explode(',',(string)$info['detail_img']);
            $info['tag'] = explode(',',(string)$info['tag']);
            $this->assign('info',$info);
            //获取相关的商品
            $modelProjectGoods = new \app\index\model\ProjectGoods();
            $config =[
                'where' => [
                    ['pg.status', '=', 0],
                    ['pg.project_id', '=', $id],
                ],'field'=>[
                    'g.id ','g.headline','g.thumb_img','g.bulk_price','g.specification','g.minimum_order_quantity',
                    'g.minimum_sample_quantity','g.increase_quantity','g.purchase_unit'
                ],'join'=>[
                    ['goods g','g.id = pg.goods_id','left']
                ]
            ];
            $goodsList= $modelProjectGoods->getList($config);
            $this->assign('goodsList',$goodsList);
            $unlockingFooterCart = unlockingFooterCartConfig([0,2,1]);
            $this->assign('unlockingFooterCart', $unlockingFooterCart);
            return $this->fetch();
        }
    }

    /**详情页
     */
    public function detailImg(){
        if(request()->isAjax()){
        }else{
            $id = intval(input('id'));
            if(!$id){
                $this->error('此项目已下架');
            }
            $model = new \app\index\model\Project();
            $config =[
                'where' => [
                    ['p.status', '=', 0],
                    ['p.shelf_status', '=', 3],
                    ['p.id', '=', $id],
                ],
            ];
            $info = $model->getInfo($config);
            if(empty($info)){
                $this->error('此商品已下架');
            }
            $info['main_img'] = explode(',',(string)$info['main_img']);
            $info['tag'] = explode(',',(string)$info['tag']);
            $this->assign('info',$info);

            //获取相关的商品
            $modelProjectGoods = new \app\index\model\ProjectGoods();
            $config =[
                'where' => [
                    ['pg.status', '=', 0],
                    ['pg.project_id', '=', $id],
                ],'field'=>[
                    'g.id ','g.headline','g.thumb_img','g.bulk_price','g.specification','g.minimum_order_quantity',
                    'g.minimum_sample_quantity','g.increase_quantity','g.purchase_unit'
                ],'join'=>[
                    ['goods g','g.id = pg.goods_id','left']
                ]
            ];
            $goodsList= $modelProjectGoods->getList($config);
            $this->assign('goodsList',$goodsList);
            $unlockingFooterCart = unlockingFooterCartConfig([0,2,1]);
            $this->assign('unlockingFooterCart', $unlockingFooterCart);
            return $this->fetch();
        }
    }

    /**获取推荐商品
     * @return array|\think\response\View
     */
    public function getRecommendGoods(){
        if(!request()->isGet()){
            return errorMsg('请求方式错误');
        }
        $id = input('get.id/d');
        //相关推荐商品
        $modelRecommendGoods = new \app\index\model\RecommendGoods();
        $config =[
            'where' => [
                ['rg.status', '=', 0],
                ['rg.goods_id', '=', $id],
            ],'field'=>[
                'g.id ','g.headline','g.thumb_img','g.bulk_price','g.specification','g.minimum_order_quantity',
                'g.minimum_sample_quantity','g.increase_quantity','g.purchase_unit'
            ],'join'=>[
                ['goods g','g.id = rg.recommend_goods_id','left']
            ]
        ];
        $list= $modelRecommendGoods->getList($config);
        $this->assign('list',$list);
        return view('goods/recommend_list_tpl');
    }
}