<?php
namespace app\api\controller;

/**
 * 收藏控制器
 */
class Collection extends \common\controller\UserBase{
    /**首页
     */
    public function index(){

        foot_cart_menu();

        return $this->fetch();
    }

    /**
     * 收藏
     */
    public function add(){
        if(!request()->isAjax()){
            $this->errorMsg('请求方式错误');
        }
        $goodsId = input('post.goods_id/d');
        if(!$goodsId){
            $this->errorMsg('没有要收藏的商品');
        }
        $model = new \app\index\model\Collection();
        $config = [
            'where'=>[
                ['user_id','=',$this->user['id']],
                ['goods_id','=',$goodsId],
                ['status','=',0]
            ] ,'field'=>[
                'id'
            ]
        ];
        $info = $model -> getInfo($config);
        if(count($info)){
            $this->successMsg('收藏成功');
        }
        $data = [
            'user_id'=>$this->user['id'],
            'goods_id'=>$goodsId,
            'create_time'=>time(),
        ];
        $result = $model -> isUpdate(false) -> save($data);
        if($result){
            $this->successMsg('收藏成功');
        }else{
            $this->errorMsg('收藏失败');
        }
    }

    /**
     * @return array|mixed
     * 查出产商相关收藏 分页查询
     */
    public function getJsonList(){
        if(!request()->isGet()){
            $this->errorMsg('请求方式错误');
        }
        $model = new \app\index\model\Collection();
        $config=[
            'where'=>[
                ['co.status', '=', 0],
                ['co.user_id', '=', $this->user['id']],
                ['g.status', '=', 0],
            ],
            'field'=>[
                'g.id ','g.headline','g.thumb_img','g.franchise_price','g.retail_price','g.sample_price','g.specification','g.minimum_order_quantity',
                'g.minimum_sample_quantity','g.increase_quantity','g.purchase_unit'
            ], 'join'=>[
                ['goods g','g.id = co.goods_id','left'],
            ],'order'=>[
                'co.create_time'=>'desc'
            ]

        ];

        $list = $model -> pageQuery($config);
        $this->successMsg('成功',$list);
/*        $currentPage = input('get.page/d');
        $this->assign('currentPage',$currentPage);
        $this->assign('list',$list);
        if(isset($_GET['pageType'])){
            $pageType = $_GET['pageType'];
            return $this->fetch($pageType);
        }*/
    }

    //删除
    public function del(){
        if(!request()->isAjax()){
            $this->errorMsg(config('custom.not_ajax'));
        }
        $goods_id = input('post.goods_id/d');

        if(empty($ids)){
            $this->errorMsg('取消失败');
        }
        $model = new \app\index\model\Collection();
        $condition = [
            ['user_id','=',$this->user['id']],
            ['goods_id','=',$goods_id],
        ];

        $result = $model -> del($condition);
        if($result['status']){
            $this->successMsg('已取消收藏');
        }else{
            $this->errorMsg('取消失败');
        }
    }
}