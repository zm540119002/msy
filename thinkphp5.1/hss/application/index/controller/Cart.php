<?php
namespace app\index\controller;

class Cart extends \common\controller\UserBase {
    /**首页
     */

    // 有结算页面
    public function index(){
        if(request()->isAjax()){
        }else{
            $unlockingFooterCart = unlockingFooterCartConfig([10,0,9]);
            $this->assign('unlockingFooterCart', $unlockingFooterCart);
            return $this->fetch();
        }
    }

    // 导航页-没有结算
    public function manage(){
        if(request()->isAjax()){
        }else{
            $unlockingFooterCart = unlockingFooterCartConfig([10,0,9]);
            $this->assign('unlockingFooterCart', $unlockingFooterCart);

            // 底部菜单，见配置文件custom.footer_menu
            $this->assign('currentPage',request()->controller().'/'.request()->action());

            return $this->fetch();
        }
    }

    /**
     * @return array
     * @throws \Exception
     *
     */
    public function addCart1(){
        if(!request()->isPost()){
            return errorMsg('请求方式错误');
        }
        $goodsList = input('post.goodsList/a');
        //return $goodsList;
        if(empty($goodsList)){
            return errorMsg('没有数据');
        }
        $userId = $this->user['id'];
        $model = new \app\index\model\Cart();
        $config = [
            'where' => [
              ['user_id','=',$userId],
              ['status','=',0],
            ],
        ];
        $cartList = $model->getList($config);

        $addData = [];
        $updateData =[];
        foreach ($goodsList as $goods){
            //假定没找到
            $find = false;
            foreach ($cartList as $cart){
                if($goods['goods_id'] == $cart['goods_id'] && $goods['buy_type'] == $cart['buy_type'] && $goods['brand_name'] == $cart['brand_name'] ){//找到了，则更新记录
                    $find = true;
                    $data = [
                        'user_id' => $this->user['id'],
                        'id' => $cart['id'],
                        'goods_id' => $cart['goods_id'],
                        'buy_type' => $cart['buy_type'] ? $cart['buy_type'] : 1,
                        'num' => $goods['num'] + $cart['num'],
                        'brand_name' => $cart['brand_name'] ? $cart['brand_name'] : '',
                        'brand_id' => $cart['brand_id'] ? $cart['brand_id'] : 0,
                    ];
                    $updateData[] = $data;

                }

            }
            if(!$find){//如果没找到，则新增
                $data = [
                    'user_id' => $this->user['id'],
                    'goods_id' => $goods['goods_id'],
                    'num' =>$goods['num'],
                    'buy_type' => $goods['buy_type'] ? $goods['buy_type'] : 1,
                    'brand_name' => $goods['brand_name'] ? $goods['brand_name'] : '',
                    'brand_id' => $goods['brand_id'] ? $goods['brand_id'] : 0,
                    'create_time'=>time(),
                ];
                $addData[] = $data;
            }
        }
        $model->startTrans();
        if(!empty($addData)){
            $res =  $model->saveAll($addData);
            if (!count($res)) {
                $model->rollback();
                return errorMsg('失败');
            }
        }
        if(!empty($updateData)){
            $res =  $model->saveAll($updateData);
            if (!count($res)) {
                $model->rollback();
                return errorMsg('失败');
            }
        }
        $model -> commit();
        return successMsg('成功');
    }

    public function addCart(){
        if(!request()->isPost()){
            $this->errorMsg('请求方式错误',config('code.error.default'));
        }
        $goodsList = input('post.goodsList/a');
        if(empty($goodsList)){
            $this->errorMsg('没有数据',config('code.error.default'));
        }
        $userId = $this->user['id'];
        $model = new \app\index\model\Cart();
        $config = [
            'field' => [
                'id','goods_id','num'
            ], 'where' => [
                ['user_id','=',$userId],
                ['status','=',0],
            ]
        ];

        $cartList = $model->getList($config);
        $cartList = array_column($cartList,null,'goods_id');
        foreach ($goodsList as $k => &$goods){

            $goods['user_id']    = $this->user['id'];
            $goods['buy_type']   = $goods['buy_type'] ? $goods['buy_type'] : 1;
            $goods['brand_name'] = $goods['brand_name'] ? $goods['brand_name'] : '';
            $goods['brand_id']   = $goods['brand_id'] ? $goods['brand_id'] : 0;
            $goods['create_time']= time();

            $carInfo = $cartList[$goods['goods_id']];

            if($carInfo != null){
                $goods['num']= $goods['num']+$carInfo['num'];
                $goods['id'] = $carInfo['id'];
            }
        }

        $model->startTrans();
        if(!empty($goodsList)){
            $res =  $model->saveAll($goodsList);
            if (!count($res)) {
                $model->rollback();
                $this->errorMsg(config('code.error.default.msg'),config('code.error.default'));
            }
        }
        $model -> commit();
        $this->successMsg('加入购物车成功！',config('code.success.default'));
    }

    /**
     * 分页查询
     */
    public function getList(){
        if(!request()->isGet()){
            return errorMsg('请求方式错误');
        }
        $userId = $this->user['id'];
        $model = new \app\index\model\Cart();
         $config=[
             'where'=>[
                 ['c.user_id','=',$userId],
//                 ['c.create_time','>',time()-7*24*60*60],//只展示7天的数据
                 ['c.status','=',0],
                 //['g.status','=',0],
             ],'join' => [
                 ['goods g','g.id = c.goods_id','left']
             ],'field'=>[
                 'c.id as cart_id','c.goods_id','c.num','c.goods_type','c.buy_type','c.create_time','c.brand_id','c.brand_name',
                 'g.id','g.headline','g.name','g.thumb_img','g.bulk_price','g.sample_price','g.specification','g.minimum_order_quantity',
                 'g.minimum_sample_quantity','g.increase_quantity','g.purchase_unit'
             ],'order'=>[
                 'c.id'=>'desc'
             ],
         ];
         $keyword = input('get.keyword','');
         if($keyword) {
             $config['where'][] = ['g.name', 'like', '%' . trim($keyword) . '%'];
         }
         $list = $model -> pageQuery($config);
        $currentPage = input('get.page/d');
        $this->assign('currentPage',$currentPage);
         $this->assign('list',$list);
         if(isset($_GET['pageType'])){
             if($_GET['pageType'] == 'index' ){
                 return $this->fetch('list_tpl');
             }
         }
    }

    /**详情页
     */
    public function detail(){
        if(request()->isAjax()){
        }else{
            $goodsId = intval(input('goods_id'));
            if(!$goodsId){
                $this->error('此商品已下架');
            }
            $model = new \app\index\model\Cart();
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
            $this->assign('info',$info);

            $unlockingFooterCart = unlockingFooterCartConfig([0,2,1]);
            $this->assign('unlockingFooterCart', $unlockingFooterCart);
            return $this->fetch();
        }
    }

    //修改购物车数量
    public function editCartNum(){
        if(!request()->isPost()){
            return errorMsg('请求方式错误');
        }
        $data = input('post.');
        $data['user_id'] = $this -> user['id'];
        $model = new \app\index\model\Cart();
        $res = $model ->isUpdate(true)-> save($data);
        if(false === $res){
            return errorMsg('失败');
        }
        return successMsg('成功');
    }

    //删除地址
    public function del(){
        if(!request()->isAjax()){
            return errorMsg(config('custom.not_ajax'));
        }
        $ids = input('post.cart_ids/a');
        $model = new \app\index\model\Cart();
        $condition = [
            ['user_id','=',$this->user['id']],
            ['id','in',$ids],
        ];
        $result = $model -> del($condition,true);
        if($result['status']){
            return successMsg('成功');
        }else{
            return errorMsg('失败');
        }
    }

    //统计购物车商品数量
    public function getCartTotalNum(){
        //统计购物车商品数量
        $user = checkLogin();
        if($user){
            $modelCart = new \app\index\model\Cart();
            $totalNum = $modelCart->where(['status'=>0,'user_id'=>$user['id']])->sum('num');
            $this -> assign('total_num',$totalNum);
        }
    }
}