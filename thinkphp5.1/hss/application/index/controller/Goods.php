<?php
namespace app\index\controller;

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
        $user = checkLogin();
        if(!$user){
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
                    'g.id','g.headline','g.name','g.thumb_img','g.bulk_price','g.sample_price','g.specification','g.minimum_order_quantity',
                    'g.minimum_sample_quantity','g.increase_quantity','g.purchase_unit', 'g.shelf_status',
                ],
            ];
            $list = $model -> pageQuery($config)->toArray();
            $showGoodsList =  $list['data'];
            foreach ($showGoodsList as $i =>&$showGoods){
                foreach($goodsList as $j=>&$goods){
                    if($showGoods['id'] == $goods['goods_id'] ){
                        $showGoodsList[$i]['num'] = $goods['num'];
                        $showGoodsList[$i]['cart_id'] = $i+1;
                    }
                }
            }
            $list['data']=$showGoodsList;
        }else{
            $userId = $user['id'];
            $model = new \app\index\model\Cart();
            $config=[
                'where'=>[
                    ['c.user_id','=',$userId],
//                 ['c.create_time','>',time()-7*24*60*60],//只展示7天的数据
                    ['c.status','=',0],
                    ['g.status','=',0],
                ],'join' => [
                    ['goods g','g.id = c.goods_id','left']
                ],'field'=>[
                    'c.id as cart_id','c.goods_id','c.num','c.create_time',
                    'g.id','g.headline','g.name','g.thumb_img','g.bulk_price','g.sample_price','g.specification','g.minimum_order_quantity',
                    'g.minimum_sample_quantity','g.increase_quantity','g.purchase_unit','g.shelf_status',
                ],'order'=>[
                    'c.id'=>'desc'
                ],
            ];
            $keyword = input('get.keyword','');
            if($keyword) {
                $config['where'][] = ['g.name', 'like', '%' . trim($keyword) . '%'];
            }
            $list = $model -> pageQuery($config);
        }
        $this->successMsg('成功',$list);
//        $currentPage = input('get.page/d');
//        $this->assign('currentPage',$currentPage);
//        $this->assign('list',$showGoodsList);
//        if(isset($_GET['pageType'])){
//            if($_GET['pageType'] == 'index' ){
//                return $this->fetch('cart/list_tpl');
//            }
//        }
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
            case config('custom.relation_type.sort'):
                $model = new \app\index\model\SortGoods();
                $field_id = 'sg.sort_id';
                $goods_id = 'sg.goods_id';
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
            //$info['purchase_specification_description'] = '10盒/箱 按箱采购'; // 假数据
            
            $this->assign('info',$info);
            $this->assign('goodsInfo',json_encode([
                'goods_id'=>$info['id'],
                'deal_price'=>$info['bulk_price'],
                'thumb_img'=>$info['thumb_img'],
                'name'=>$info['name'],
                'specification'=>preg_replace('//s*/', '', $info['specification']),
            ]));
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
            Cart::getCartTotalNum();
            $unlockingFooterCart = unlockingFooterCartConfigTest([0,2,1,3]);
            array_push($unlockingFooterCart['menu'][0]['class'],'group_btn30');
            array_push($unlockingFooterCart['menu'][1]['class'],'group_btn30');
            array_push($unlockingFooterCart['menu'][2]['class'],'group_btn30');
            array_push($unlockingFooterCart['menu'][3]['class'],'group_btn30');
            $this->assign('unlockingFooterCart',json_encode($unlockingFooterCart));
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

        $list = $model -> pageQuery($condition);
        $this->successMsg('成功',$list);
        //return $model -> pageQuery($condition);
        return $model->getLastSql();
    }
}