<?php
namespace Purchase\Controller;
use Think\Controller;
use Common\Lib\AuthUser;
use Purchase\Controller\BaseAuthUserController;
class CartController extends BaseBaseController {
    //前台首页
    public function index(){
        
        $this->display();
    }
    //加入购物车
    public function addCart(){
        if(IS_POST){
            $goodsId = intval($_POST['id']);
            $goodsNum = isset($_POST['goodsNum']) && intval($_POST['goodsNum'])>0 ? $_POST['goodsNum'] : 1;
            $user = AuthUser::getSession();
            //已登录
            if(isset($user)  && !empty($user)){
                $cartModel = D("cart");
                $userId  = $user['id'];
                if($goodsId <= 0 ){
                    $this -> ajaxReturn(errorMsg('参数有误'));
                }
                $cartInfo = $cartModel -> getCartInfoByGoodsId($userId,$goodsId);
                if(empty($cartInfo)){//添加
                    $result = $cartModel -> addCart($userId,$goodsId,$goodsNum);
                    if(!$result){
                        show(-1,'添加购物车失败');
                    }
                }else{//修改
                    if(isset($_POST['goodsNum'])&& intval($_POST['goodsNum'])>0){
                        $goodsNum = intval($_POST['goodsNum']);
                    }else{
                        $goodsNum = $cartInfo['goods_num']+ $goodsNum;
                    }

                    $result   = $cartModel-> updateGoodsNum($userId,$goodsId,$goodsNum);
                    if(false === $result){
                        show(-1,'更新失败');
                    }
                }
                //返回购物车的数量和总价
                $cartList = D('cart') -> getCartList($userId);
                $cartIds  = D('cart') -> getCartIds($userId);
                $cartInfo = D('cart') -> getCartInfo($cartList);
                $cartInfo['cartIds'] = $cartIds;
                $this -> ajaxReturn(successMsg($cartInfo));
            }
            //没有登录

            if(isset($_POST['goodsNum'])&& intval($_POST['goodsNum'])){//修改
                $curCartArray = session('cart');
                foreach ($curCartArray as $k =>$v){
                   if($v['id'] == $_POST['id']){
                       $curCartArray[$k]['num'] = $_POST['goodsNum'];
                       session('cart',$curCartArray);

                   }
                }
            }else{//添加
                $curCartArray=$_SESSION['cart'];
                if($curCartArray==""){
                    $cartInfo[0]["id"]=$goodsId;//商品id保存到二维数组中
                    $cartInfo[0]["num"]=$goodsNum;//商品数量保存到二维数组中
                    session('cart',$cartInfo);

                }else {
                    //返回数组键名倒序取最大
                    $ar_keys = array_keys($curCartArray);
                    $len = count($ar_keys);
                    $maxArrayKeyId = $ar_keys[$len - 1] + 1;
                    $is_exist=$this->isexist($goodsId,$goodsNum,$curCartArray);
                    if($is_exist==false){
                        $curCartArray[$maxArrayKeyId]["id"] = $goodsId;
                        $curCartArray[$maxArrayKeyId]["num"] = $goodsNum;
                        session('cart',$curCartArray);
                    }else{
                        $arr_exist=explode("/",$is_exist);
                        $id=$arr_exist[0];
                        $num=$arr_exist[1];
                        $curCartArray[$id]["num"]=$num;
                        session('cart',$curCartArray);
                    }
                }
            }

            //返回购物车的商品数量和总价
            $cartList = D('cart') -> getCartListBySession();
            $cartInfo = D('cart') -> getCartInfo($cartList);
            $this -> ajaxReturn(successMsg($cartInfo));
        }
    }


    //判断购物车是否存在相同商品
    public function isexist($id,$num,$array)
    {
        $isexist=false;
        foreach($array as $key1=>$value)
        {
            foreach($value as $key=>$arrayid)
            {
                if($key=="id" && $arrayid==$id)
                {
                    $num=$value["num"]+$num;
                    $isexist=$key1."/".$num;
                }
            }
        }
        return $isexist;
    }
  
   //购物车信息
    public function myCart(){
        $user = AuthUser::getSession();
        if(isset($user)){
            $cartList = D('cart') ->getCartList($user['id']);
        }else{
            //没有登录
            $cartList = D('cart') ->getCartListBySession();
        }
        $cartInfo = D('cart') -> getCartInfo($cartList);
        $this -> cartList = $cartList;
        $this -> cartInfo = $cartInfo;
        $this -> display();
    }
    //删除购物车信息
    public function delCart(){
        if(IS_POST){
            $user = AuthUser::getSession();
            //已登录
            if(isset($user) && !empty($user)){
                //删除单条购物车信息
                if(isset($_POST['cartId']) && $_POST['cartId']){
                    $cartId = I('post.cartId','','int');
                    if($cartId<=0 && empty($cartId)){
                        $this -> ajaxReturn(errorMsg('参数错误'));
                    }
                    if(isset($user)){
                        $where['user_id']  = $user['id'];
                        $where['id'] = $cartId;
                        $result = M('cart') -> where($where)->delete();
                    }

                }
                //清空购物车
                if(is_array(I('post.cartIds')) && !empty(I('post.cartIds'))){
                    $cartIds = I('post.cartIds');
                    if(!is_array($cartIds) && empty($cartIds)){
                        $this -> ajaxReturn(errorMsg('参数错误'));
                    }
                    if(isset($user)){
                        $where['user_id']  = $user['id'];
                        $where['id'] =  array('in', $cartIds);
                        $result = M('cart') -> where($where)->delete();
                    }
                }
                if(!$result){
                    $this -> ajaxReturn(errorMsg('删除失败'));
                }else{
                    $this -> ajaxReturn(successMsg('删除成功'));
                }
            }
            
           //没有登录
            if(isset($_POST['goodsId']) && intval($_POST['goodsId'])){//删除单条数据
                $curCartArray =session('cart');
                foreach ($curCartArray as $k =>$v){
                    if($v['id'] == $_POST['goodsId']){
                        unset($curCartArray[$k]);
                    }
                }
                session('cart',$curCartArray);
                $this -> ajaxReturn(successMsg('删除成功'));
            }
            
            //删除整个购物车
          session('cart',null);
            $this -> ajaxReturn(successMsg('删除成功'));
        }
    }


    
}