<?php
namespace app\weiya_customization\controller;

class Goods extends \common\controller\Base{
    /**首页
     */
    public function index(){
        if(request()->isAjax()){
        }else{
            return $this->fetch();
        }
    }

    /**
     * 查出产商相关产品 分页查询
     */
    public function getList(){
        if(!request()->isGet()){
            return errorMsg('请求方式错误');
        }
        $model = new \app\weiya_customization\model\Goods();
        $config=[
            'where'=>[
            ],
            'field'=>[
                'g.id,g.sale_price,g.sale_type,g.shelf_status,g.create_time,g.update_time,g.inventory,
                g.name,g.retail_price,g.trait,g.category_id_1,g.category_id_2,g.category_id_3,
                g.thumb_img,g.goods_video,g.main_img,g.details_img,g.tag,g.parameters,g.sort,g.trait'
            ],
            'order'=>[
                'sort'=>'desc',
                'line_num'=>'asc',
                'id'=>'desc'
            ],
        ];
        if(input('?get.storeId') && (int)input('?get.storeId')){
            $config['where'][] = ['g.store_id', '=', input('get.storeId')];
        }
        $keyword = input('get.keyword','');
        if($keyword) {
            $config['where'][] = ['name', 'like', '%' . trim($keyword) . '%'];
        }
        $list = $model -> pageQuery($config);
        $this->assign('list',$list);
        if(isset($_GET['pageType'])){
            if($_GET['pageType'] == 'store' ){//店铺产品列表
                return $this->fetch('list_tpl');
            }
        }
    }

    /**商品详情页
     */
    public function detail(){
        if(request()->isAjax()){
        }else{
            $goodsId = intval(input('goods_id'));
            if(!$goodsId){
                $this->error('此商品已下架');
            }
            $model = new \app\weiya_customization\model\Goods();
            $config =[
                'where' => [
                    ['g.status', '=', 0],
                    ['g.shelf_status', '=', 3],
                    ['g.id', '=', $goodsId],
                ],
            ];
            $info = $model->getInfo($config);
            if(empty($info)){
                $this->error('此商品已下架');
            }
            $info['main_img'] = explode(',',(string)$info['main_img']);
            $info['detail_img'] = explode(',',(string)$info['detail_img']);
            $info['tag'] = explode(',',(string)$info['tag']);
            $this->assign('info',$info);
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
        $config =[
            'where' => [
                ['rg.status', '=', 0],
                ['rg.goods_id', '=', $goodsId],
            ],'field'=>[
                'rg.recommend_goods_id',
            ]
        ];
        $modelRecommendGoods = new \app\weiya_customization\model\RecommendGoods();
        $recommendGoodsIds = $modelRecommendGoods->getList($config);
        $recommendGoodsIds = array_column($recommendGoodsIds,'recommend_goods_id');

        $config =[
            'where' => [
                ['g.status', '=', 0],
                ['g.shelf_status', '=', 3],
                ['g.id', 'in', $recommendGoodsIds],
            ],'field'=>[
                'g.id as goods_id','g.headline','g.thumb_img','g.bulk_price'
            ]
        ];
        $model = new \app\weiya_customization\model\Goods();
        $list = $model->getList($config);
        $this->assign('list',$list);
        return view('goods/recommend_list_tpl');
    }
}