<?php
namespace Purchase\Model;
use Think\Model;
class CartModel extends Model {

    //获取购物车列表
    public function getCartList($uid){
        $cartList = M('cart') -> where( array( 'user_id'=>$uid ) ) -> select();
        $delTime = C('DEFAULT_DUE_TIME');
        $this -> delCartByTime($cartList,$uid,$delTime);
        if(isset($_SESSION['cart'])&& !empty($_SESSION['cart'])){
           $cartList = M('cart') -> where( array( 'user_id'=>$uid ) ) -> select();
           $allGoodsIds = array();//session存在的总用goodsIds
           $sameGoodsIds = array();//相同的ID
           foreach ($_SESSION['cart'] as $k=>$v){
               $allGoodsIds[]= $v['id'];
              foreach ($cartList as $kk => $vv){
                  if($v['id'] == $vv['goods_id']) {//修改数据库数量
                      $sameGoodsIds[] = $v['id'];
                      $goodsNum = $v['num'] + $vv['goods_num'];
                      $this->updateGoodsNum($uid, $v['id'], $goodsNum);
                  }
              }
           }
           $addGoodsIds = array_diff($allGoodsIds,$sameGoodsIds);//不同goodsIds
            foreach ($addGoodsIds as $k=>$v){//添加到数据库
                foreach ($_SESSION['cart'] as $kk=>$vv){
                    if($v == $vv['id']){
                        $this ->addCart($uid,$vv['id'],$vv['num']);
                    }
                }

            }
            unset ($_SESSION['cart']);
        }
        $cartList = M('cart') -> where( array( 'user_id'=>$uid ) ) -> select();
        return $cartList;
    }

    //获取购物车列表ids
    public function getCartIds($uid){
        $cartIds = M('cart') -> where( array( 'user_id'=>$uid ) ) -> getField('id',true);
        $cartIds = implode(',',$cartIds);
        return $cartIds;
    }
    //定时更新购物车的信息
    public function delCartByTime($cartList,$uid,$delTime){
        foreach ($cartList as $k=>$v){
            if(time() - intval($v['create_time']) >$delTime ) {
                $where['id'] = $v['id'];
                $where['user_id'] = $uid;
                M('cart') ->where($where)-> delete();

            }
        }
    }

    //获取购物车的总数和总商品数量
    public function getCartInfo($cartList){
        $total = 0;
        $count = 0;
        foreach ($cartList as $k => $v ){
            $minTotal = $v['goods_num'] * $v['goods_price'];
            $total   += $minTotal;
            $count   += $v['goods_num'];
        }
        $cartInfo['total'] = $total;
        $cartInfo['count'] = $count;
        return $cartInfo;
    }

    //查找客户购物车的某件商品信息
    public function getCartInfoByGoodsId($uid,$goodsId){
        $where['user_id']  = $uid;
        $where['goods_id'] = $goodsId;
        $cartInfo =  M('cart') -> where($where) -> find();
        return $cartInfo;
    }

    //更新购物车数量
    public function updateGoodsNum($uid,$goodsId,$goods_num){
        $where['user_id']  = $uid;
        $where['goods_id'] = $goodsId;
        $result= M('cart') -> where($where)->setField('goods_num',$goods_num);
        return $result;
    }

    //加入购物车
    public function addCart($uid,$goodsId,$goodsNum){
        $goodsInfo = M('goods')->where(array('id'=>$goodsId))->find();
        $data['user_id']     = $uid;
        $data['goods_num']   = $goodsNum;
        $data['goods_id']    = $goodsInfo['id'];
        $data['goods_name']  = $goodsInfo['name'];
        $data['goods_price'] = $goodsInfo['price'];
        $data['goods_img']   = $goodsInfo['first_img'];
        $data['create_time'] = time();
        $result = M('cart') -> add($data);
        return $result;
    }


    //根据cartIds查找客户购物车的商品信息
    public function getCartListByCartIds($uid,$cartIds){
        $where['user_id']  = $uid;
        $where['id'] = array('in',$cartIds);
        $cartList =  M('cart') -> where($where) -> select();
        return $cartList;
    }

    //根据cartId删除购物车
    public function delCartByCartIds($uid,$cartIds){
        $where['user_id'] = $uid;
        $where['id']      = array('in',$cartIds);
        $result = M('cart')->where($where)->delete();
        return $result;
    }


    //查询session上购物车列表
    public function getCartListBySession(){
        foreach ($_SESSION['cart'] as $k=>$v){
            $where['id'] = $v['id'];
            $goodsInfo =  M('goods') -> where($where) ->field('id,name,price,first_img') ->find();
            $cartInfo  = array(
                'goods_id' => $goodsInfo['id'],
                'goods_name' =>$goodsInfo['name'],
                'goods_price' =>$goodsInfo['price'],
                'goods_img'=>$goodsInfo['first_img'],
                'goods_num'=>$v['num'],
            );
            $cartList[] = $cartInfo;

        }
        return $cartList;
    }

    
}