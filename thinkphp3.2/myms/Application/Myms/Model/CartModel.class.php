<?php
namespace Myms\Model;
use Think\Model;
use web\all\Lib\AuthUser;
class CartModel extends Model {
    //登录时加入购物车
    public function addCart($uid,$cartInfo){
        $data['user_id']       = $uid;
        $data['num']           = $cartInfo['num'];
        $data['foreign_id']    = $cartInfo['foreign_id'];
        $data['type']          = $cartInfo['type'];
        $data['create_time']   = time();
        $result = M('cart') -> add($data);
        return $result;
    }

    //登录后获取购物车列表
    public function getCartList($uid){
        $where = array(
            'user_id' =>$uid
        );
        $cartList = $this -> selectCart($where);
        $delTime = C('DEFAULT_DUE_TIME');
        $this -> delCartByTime($cartList,$uid,$delTime);
        $this->addCartBySession($uid);
        $cartList = $this -> selectCart($where);
        $newCartList =[];
        foreach ($cartList as $k=>$v){
            if($v['type'] == 1){//商品
                $goodsInfo =  D('Goods') ->getGoodsInfoByGoodsId($v['foreign_id']);
            }
            if($v['type'] == 2){//项目
                $goodsInfo =  D('Project')->getProjectInfoByProjectId($v['foreign_id']);
            }
            $cartInfo['id']           =  $v['id'];
            $cartInfo['foreign_id']   =  $goodsInfo['id'];
            $cartInfo['foreign_name'] =  $goodsInfo['name'];
            $cartInfo['img']          =  $goodsInfo['main_img'];
            $cartInfo['price']        =  $goodsInfo['real_price'];
            $cartInfo['num']          =  $v['num'];
            $cartInfo['type']         =  $v['type'];
            $cartInfo['buy_type']     =  $goodsInfo['buy_type'];
            $newCartList[] = $cartInfo;
        }
        return $newCartList;
    }

    //登录后，session的产品入库
    public function addCartBySession($uid){
        if(session('?cart') &&! empty(session('cart'))){
            $where = array(
                'user_id' =>$uid
            );
            $cartList = $this -> selectCart($where);
            $allIds = array();//session存在的总goodsIds
            $sameIds = array();//相同的ID
            $cartIds = '';
            foreach (session('cart') as $k=>$v){
                $allIds[]= $k;
                foreach ($cartList as $kk => $vv){
                    if($v['id'] == $vv['foreign_id'] && $v['type'] == $vv['type']) {//修改数据库数量
                        $sameIds[] = $k;
                        $goodsNum = $v['num'] + $vv['num'];
                        $this->updateGoodsNum($uid, $v['id'],$goodsNum,$v['type']);
                        $cartId=  $v['id'];
                        $cartIds=$cartId.','.$cartIds;
                    }
                }
            }
            $addIds = array_diff($allIds,$sameIds);//不同goodsIds
            foreach ($addIds as $k=>$v){//添加到数据库
                foreach (session('cart') as $kk=>$vv){
                    if($v == $kk){
                        if($vv['type'] == 1){
                            $addCartInfo = D('Goods')->getGoodsInfoByGoodsId($vv['id']);
                        }
                        if($vv['type'] == 2){
                            $addCartInfo = D('Project')->getProjectInfoByProjectId($vv['id']);
                        }
                        $addCartInfo['num'] = $vv['num'];
                        $addCartInfo['foreign_id'] = $vv['foreign_id'];
                        $addCartInfo['type'] = $vv['type'];
                        $cartId = $this ->addCart($uid,$addCartInfo);
                        $cartIds=$cartId.','.$cartIds;
                    }
                }
            }
            session('cart',null);
            return $cartIds;
        }
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
            $minTotal = $v['num'] * $v['price'];
            $total   += $minTotal;
            $count   += $v['num'];
        }
        $cartInfo['total'] = $total;
        $cartInfo['count'] = $count;
        return $cartInfo;
    }

    //查找客户购物车的某件商品信息
    public function getCartInfoByForeignId($uid,$foreignId,$type){
        $where['user_id']  = $uid;
        $where['foreign_id'] = $foreignId;
        $where['type'] = $type;
        $cartInfo =  M('cart') -> where($where) -> find();
        return $cartInfo;
    }

    //更新购物车数量
    public function updateCartNum($uid,$foreignId,$goods_num,$type){
        $where['user_id']  = $uid;
        $where['foreign_id'] = $foreignId;
        $where['type'] = $type;
        $result= M('cart') -> where($where)->setField('num',$goods_num);
        return $result;
    }



    //根据cartIds查找客户购物车的商品信息
    public function getCartListByCartIds($uid,$cartIds){
        $where['user_id']  = $uid;
        $where['id'] = array('in',$cartIds);
        $cartList =  M('cart') -> where($where) -> select();
        return $cartList;
    }
    //查找24小时内客户添加购物车的商品信息
    public function getCartListByTime($uid){
        $where['user_id']  = $uid;
        $where['create_time'] =array(array('gt',time()-24*60*60),array('lt',time())) ;
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
        $cartList = [];
        foreach (session('cart') as $k=>$v){
            if($v['type'] == 1){//商品
                $goodsInfo =  D('goods') ->getGoodsInfoByGoodsId($v['foreign_id']);
            }
            if($v['type'] == 2){//项目
                $goodsInfo =  D('project')->getProjectInfoByProjectId($v['foreign_id']);
            }
            $cartInfo['foreign_id']   =  $goodsInfo['id'];
            $cartInfo['foreign_name'] =  $goodsInfo['name'];
            $cartInfo['img']          =  $goodsInfo['main_img'];
            $cartInfo['price']        =  $goodsInfo['real_price'];
            $cartInfo['num']          =  $v['num'];
            $cartInfo['type']         =  $v['type'];
            $cartInfo['buy_type']     =  $goodsInfo['buy_type'];
            $cartList[] = $cartInfo;
        }
        return $cartList;
    }
    
    //查询购物车表
    public function selectCart($where=[],$field=[],$join=[],$order=[],$limit=''){
        $_where = array(

        );
        $_field = array(
            'c.id','c.user_id','c.foreign_id','c.num','c.create_time','c.type',
        );
        $_join = array(

        );
        $_order = array(

        );
        $list = $this
            ->alias('c')
            ->where(array_merge($_where,$where))
            ->field(array_merge($_field,$field))
            ->join(array_merge($_join,$join))
            ->order(array_merge($_order,$order))
            ->limit($limit)
            ->select();
        return $list?:[];
    }

    //获取购物车的总价、商品数和CartId
    public function getAllCartInfo(){
        $this->user = AuthUser::check();
        if(isset($user)  && !empty($user)){
            $cartList = $this -> getCartList($user['id']);
            $cartIds = $this->getCartIds($user['id']);
        }else{
            $cartList = $this -> getCartListBySession();
        }
        $cartInfo = $this -> getCartInfo($cartList);
        $cartInfo['cartIds'] = $cartIds;
        return $cartInfo;
    }
    //登录或没有登录获取购物车列表
    public function cartList(){
        $this->user = AuthUser::check();
        if(isset($this->user) && !empty($this->user['id'])){
            $cartList = $this ->getCartList($this->user['id']);
        }else{
            //没有登录
            $cartList = $this ->getCartListBySession();
        }
        return $cartList;

    }
}