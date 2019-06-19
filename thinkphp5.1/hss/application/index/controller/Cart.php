<?php
namespace app\index\controller;

class Cart extends \common\controller\UserBase {
    /**
     * 加入购物车
     * @throws \think\exception\PDOException
     */
    public function addCart(){
        if(!request()->isPost()){
            $this->errorMsg('请求方式错误',config('code.error.default'));
        }
        $goodsList = input('post.data/a');
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
        $validate = new \app\index\validate\Cart();
        foreach ($goodsList as $k => &$goods){
            if(!$validate->check($goods)) {
                $this->errorMsg($validate->getError(),config('code.error.default'));
            }
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

    //删除地址
    public function del(){
        if(!request()->isAjax()){
            return errorMsg(config('custom.not_ajax'));
        }
        $goodsList = input('post.data/a');
        if(empty($goodsList)){
            $this->errorMsg('没有数据！',config('code.error.default'));
        }
        $goodsIds = [];
        foreach ($goodsList as $item=>$value){
            $goodsIds[] = $value['goods_id'];
        }
        $model = new \app\index\model\Cart();
        $condition = [
            ['user_id','=',$this->user['id']],
            ['goods_id','in',$goodsIds],
        ];
        $result = $model -> del($condition,true);
        if($result['status']){
            $this->successMsg('删除成功！',config('code.success.default'));
        }else{
            $this->errorMsg('删除失败！',config('code.default'));
        }
    }
}