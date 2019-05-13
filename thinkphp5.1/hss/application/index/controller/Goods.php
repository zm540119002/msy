<?php
namespace app\index\controller;

use function Sodium\crypto_box_publickey_from_secretkey;

class Goods extends \common\controller\Base{
    /**首页
     */
    public function index(){
        if(request()->isAjax()){
        }else{
            return $this->fetch();
        }
    }

    //分类相关的商品
    public function goodsWitchCategory(){
        $categoryId = intval(input('category_id'));
        if(!$categoryId){
            $this->error('没有此分类');
        }
        $modelGoodsCategory = new \app\index\model\GoodsCategory();
        $config =[
            'where' => [
                ['gc.status', '=', 0],
                ['gc.id', '=', $categoryId],
                ['gc.level','=',1]
            ],
        ];
        $info = $modelGoodsCategory->getInfo($config);
        $info['tag'] = explode(',',(string)$info['tag']);
        if(empty($info)){
            $this->error('没有此分类');
        }
        $this->assign('info',$info);

        //获取相关的商品
        $model = new \app\index\model\Goods();
        $config =[
            'where' => [
                ['g.status', '=', 0],
                ['g.category_id_1', '=', $categoryId],
                ['g.shelf_status', '=', 3],
            ],'field'=>[
                'g.id ','g.headline','g.thumb_img','g.bulk_price','g.specification','g.minimum_order_quantity',
                'g.minimum_sample_quantity','g.increase_quantity','g.purchase_unit'
            ],
        ];
        $goodsList= $model->getList($config);
        $this->assign('goodsList',$goodsList);
        $unlockingFooterCart = unlockingFooterCartConfig([0,2,1]);
        $this->assign('unlockingFooterCart', $unlockingFooterCart);
        return $this->fetch();
    }

    /**
     * 查出产商相关产品 分页查询
     */
    public function getList(){

        if(!request()->isGet()){
            return errorMsg('请求方式错误');
        }
        $model = new \app\index\model\Goods();
        $config=[
            'where'=>[
                ['g.status', '=', 0],
                ['g.shelf_status', '=', 3],
            ],
            'field'=>[
                'g.id ','g.headline','g.thumb_img','g.bulk_price','g.sample_price','g.specification','g.minimum_order_quantity',
                'g.minimum_sample_quantity','g.increase_quantity','g.purchase_unit'
            ],
            'order'=>[
                'is_selection'=>'desc',
                'sort'=>'desc',
                'id'=>'desc'
            ],
        ];
        if(input('?get.category_id') && input('get.category_id/d')){
            $config['where'][] = ['g.category_id_1', '=', input('get.category_id/d')];
        }
        if(input('get.belong_to/d')){
            $config['where'][] = ['g.belong_to', '=', input('get.belong_to/d')];
        }
        if(input('get.is_selection/d')){
            $config['where'][] = ['g.is_selection', '=', input('get.is_selection/d')];
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
                case 'index': return $this->fetch('list_goods_two_column_tpl'); break;  // 一行两个
                case 'sort' : return $this->fetch('list_goods_one_column_tpl'); break;   // 一行一个
            }
        }
    }

    /**
     * 未登录状态  查出相关购物车产品信息 分页查询
     */
    public function getCartGoodsList(){
        if(!request()->isGet()){
            return errorMsg('请求方式错误');
        }
        $cartList = input('get.cartList');
        $goodsList =  json_decode($cartList,true)['goodsList'];
        $goodsIds = array_column($goodsList,'goods_id');
        $model = new \app\index\model\Goods();
        $config=[
            'where'=>[
                ['g.status', '=', 0],
                ['g.id', 'in', $goodsIds],
            ],
            'field'=>[
                'g.id ','g.headline','g.thumb_img','g.bulk_price','g.sample_price','g.specification','g.minimum_order_quantity',
                'g.minimum_sample_quantity','g.increase_quantity','g.purchase_unit'
            ],
        ];

        $list = $model -> pageQuery($config)->each(function ($item, $key){
            foreach($goodsList as $k=>&$v){
                if($v['goods_id'] == $item['id'] ){
                    $item['num'] = 6;
                }
            }
            return $item;
        });
        return $list;
        $this->assign('list',$list);

        if(isset($_GET['pageType'])){

            // 排列的数量不同
            switch($_GET['pageType']){
                case 'index': return $this->fetch('list_goods_two_column_tpl'); break;  // 一行两个
                case 'sort' : return $this->fetch('list_goods_one_column_tpl'); break;   // 一行一个
            }
        }
    }
    /***
     * 获取各关联表下的商品 -通用方法
     * @return array|\think\response\View
     */
    public function getRelationGoods(){
        if(!request()->isGet()){
            return errorMsg('参数有误');
        }
        if(!$id = input('get.id/d')) return errorMsg('参数有误');

        $relation = input('get.relation/d');
        // custom.php relation_type
        switch($relation){
            case config('custom.relation_type.scene'):
                $model = new \app\index\model\SceneGoods();
                $field_id = 'sg.scene_id';
                $goods_id = 'sg.goods_id';
                break;
            case config('custom.relation_type.project'):
                $model = new \app\index\model\ProjectGoods();
                $field_id = 'pg.project_id';
                $goods_id = 'pg.goods_id';
                break;
            case config('custom.relation_type.promotion'):
                $model = new \app\index\model\PromotionGoods();
                $field_id = 'pg.promotion_id';
                $goods_id = 'pg.goods_id';
                break;
            default:
                return errorMsg('参数有误');
        }

        $condition = [
            'where' => [
                [$field_id,'=',$id], ['g.status','=', 0], ['g.shelf_status','=', 3],
            ],'join' => [
                ['goods g','g.id = '.$goods_id,'left'],
            ],'field' => [
                'g.id ','g.headline','g.thumb_img','g.bulk_price','g.specification','g.minimum_order_quantity',
                'g.minimum_sample_quantity','g.increase_quantity','g.purchase_unit'
            ],
        ];

        $list = $model -> pageQuery($condition);
        $this->successMsg('成功',$list);
    }

    /**
     * 商品详情页
     */
    public function detail(){
        if(request()->isAjax()){
        }else{
            // 商品基础处理
            if(!$id=input('id/d')) $this->error('此商品已下架');
            $model = new \app\index\model\Goods();
            $config =[
                'where' => [
                    ['g.status', '=', 0],
                    ['g.shelf_status', '=', 3],
                    ['g.id', '=', $id],
                ],
            ];
            $info = $model->getInfo($config);

            if(empty($info)) $this->error('此商品已下架');

            $info['main_img'] = explode(',',(string)$info['main_img']);
            $info['detail_img'] = explode(',',(string)$info['detail_img']);
            $info['tag'] = explode(',',(string)$info['tag']);
            $this->assign('info',$info);


            $modelComment = new \app\index\model\Comment();
            $where = [
                ['status','=',0],
                ['goods_id','=',$id],
            ];
            $averageScore = $modelComment -> where($where)->avg('score');
            $averageScore = round($averageScore,2);
            $this ->assign('averageScore',$averageScore);
            $total = $modelComment -> where($where)->count('user_id');
            $this ->assign('total',$total);


            //登录判断是否已收藏
            $user = session('user');
            if(!empty($user)){
                $modelCollection = new \app\index\model\Collection();
                $config = [
                    'where'=>[
                        ['user_id','=',$user['id']],
                        ['goods_id','=',$id],
                        ['status','=',0]
                    ],'field'=>[
                        'id'
                    ]
                ];
                $info = $modelCollection -> getInfo($config);
                if(count($info)){
                    $this->assign('collected', 1);
                }
            }

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
        $goodsId = input('get.goods_id/d');
        //相关推荐商品
        $modelRecommendGoods = new \app\index\model\RecommendGoods();
        $config =[
            'where' => [
                ['rg.status', '=', 0],
                ['rg.goods_id', '=', $goodsId],
            ],'field'=>[
                'g.id ','g.headline','g.thumb_img','g.bulk_price','g.specification','g.minimum_order_quantity',
                'g.minimum_sample_quantity','g.increase_quantity','g.purchase_unit'
            ],'join'=>[
                ['goods g','g.id = rg.recommend_goods_id','left']
            ]
        ];
        $list = $modelRecommendGoods->getList($config);
        $this->assign('list',$list);
        return view('goods/recommend_list_tpl');
    }

    /**
     * 获取套餐商品
     */
    public function getPromotionGoods(){
        if(!request()->isGet()){
            return errorMsg('参数有误');
        }
        if(!$id = input('get.id/d')) return errorMsg('参数有误');

        $model = new \app\index\model\PromotionGoods();
        $condition = [
            'field' => [
                'g.id ','g.headline','g.thumb_img','g.bulk_price','g.specification','g.minimum_order_quantity',
                'g.minimum_sample_quantity','g.increase_quantity','g.purchase_unit'
            ], 'where' => [
                ['pg.promotion_id','=',$id], ['g.status','=', 0], ['g.shelf_status','=', 3],
            ],'join' => [
                ['goods g','g.id = pg.goods_id','left'],
            ],
        ];

        return $model -> pageQuery($condition);
        return $model->getLastSql();
    }
}