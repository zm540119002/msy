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

    /**
     * 加入购物车
     * @throws \think\exception\PDOException
     */
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
        return input('post.');
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