<?php
namespace Mall\Controller;
use Think\Controller;
use web\all\Lib\AuthUser;
use web\all\Controller\BaseController;
class CartController extends BaseController {
    //加入购物车
    public function addCart(){
        if(IS_POST){
            $num = isset($_POST['num']) && intval($_POST['num']) ? $_POST['num'] : 1;
            if(AuthUser::check()){//商品添加
                $foreignId = intval($_POST['goodsId']);
                $type = 1;
            }
            if(isset($_POST['projectId']) && !empty($_POST['projectId'])){//项目添加
                $foreignId = intval($_POST['projectId']);
                $type = 2;
            }
            $addCartInfo['num'] = $num;
            $addCartInfo['type'] = $type;
            $addCartInfo['foreign_id'] = $foreignId;
            if(isset($_POST['foreignId']) && !empty($_POST['foreignId'])){//购物车修改数量
                $foreignId = intval($_POST['foreignId']);
                $type =  intval($_POST['type']);
            }

            //已登录
            if(AuthUser::check()){
                $model = D('Cart');
                $userId  =  $this->user['id'];
                $cartInfo = $model -> getCartInfoByForeignId($userId,$foreignId,$type);
                if(empty($cartInfo)){//添加
                    $result = $model -> addCart($userId,$addCartInfo);
                    $addCartInfo['cart_id'] = $result;
                    if(!$result){
                        $this -> ajaxReturn(errorMsg('添加购物车失败'));
                    }else{
                        $this -> ajaxReturn(errorMsg('添加购物车成功'));
                    }
                }else{//修改
                    if(isset($_POST['num'])&& intval($_POST['num'])){
                        $num = intval($_POST['num']);
                    }else{
                        $num = $cartInfo['num'] + $num;
                    }
                    $result   = $model-> updateCartNum($userId,$foreignId,$num,$type);
                    $addCartInfo['cart_id'] = $cartInfo['id'];
                    if(false === $result){
                        $this -> ajaxReturn(errorMsg('更新购物车失败'));
                    }else{
                        $this -> ajaxReturn(errorMsg('修改数量成功'));
                    }
                }

            }else{ //没有登录
                if(isset($_POST['foreignId']) && intval($_POST['foreignId'])){//修改
                    $curCartArray = session('cart');
                    foreach ($curCartArray as $k =>$v){
                        if(intval($v['foreign_id']) == $foreignId && intval($v['type']) == $type){
                            $curCartArray[$k]['num'] = $_POST['num'];
                            session('cart',$curCartArray);
                            $this -> ajaxReturn(successMsg('修改数量成功'));
                        }
                    }
                }else{//添加
                    $curCartArray = session('cart');
                    if($curCartArray==""){
                        $cartInfo[0]["foreign_id"]=$foreignId;//商品id保存到二维数组中
                        $cartInfo[0]["num"]=$num;//商品数量保存到二维数组中
                        $cartInfo[0]["type"]=$type;//商品数量保存到二维数组中
                        session('cart',$cartInfo);

                    }else {
                        //返回数组键名倒序取最大
                        $ar_keys = array_keys($curCartArray);
                        $len = count($ar_keys);
                        $maxArrayKeyId = $ar_keys[$len - 1] + 1;
                        $is_exist=$this->isExist($foreignId,$num,$curCartArray,$type);
                        if($is_exist==false){
                            $curCartArray[$maxArrayKeyId]["foreign_id"] = $foreignId;
                            $curCartArray[$maxArrayKeyId]["num"] = $num;
                            $curCartArray[$maxArrayKeyId]["type"] = $type;
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
                $this -> ajaxReturn(successMsg('加入购物车成功'));
            }
        }
    }


    //判断购物车是否存在相同商品
    public function isExist($id,$num,$array,$type)
    {
        $isExist=false;
        foreach($array as $key1=>$value)
        {
            if(intval($value['foreign_id']) == $id && intval($value['type'])==$type){
                $num=$value["num"]+$num;
                $isExist=$key1."/".$num;
            }
        }
        return $isExist;
    }

    //购物车信息
    public function myCart(){
        if(AuthUser::check()){
            $cartList = D('Cart') ->getCartList( $this->user['id']);
        }else{
            //没有登录
            $cartList = D('Cart') ->getCartListBySession();
        }
        $cartInfo = D('Cart') -> getCartInfo($cartList);
        $this -> cartList = $cartList;
        $this -> cartInfo = $cartInfo;
        $this -> display();
    }
    //删除购物车信息
    public function delCart(){
        if(IS_POST){
            //已登录
            if(  $this->user = AuthUser::check()){
                //删除单条购物车信息
                if(isset($_POST['cartId']) && $_POST['cartId']){
                    $where['user_id']  =  $this->user['id'];
                    $where['id']  = $_POST['cartId'];
                    $result = M('cart') -> where($where)->delete();
                }
                if(isset($_POST['cartIds']) && $_POST['cartIds']){
                    $where['user_id']  =  $this->user['id'];
                    $where['id']  = array('in',$_POST['cartIds']);
                    $result = M('cart') -> where($where)->delete();
                }
                if(!$result){
                    $this -> ajaxReturn(errorMsg('删除失败'));
                }else{
                    $this -> ajaxReturn(successMsg('删除成功'));
                }
            }else{//没有登录

                if(isset($_POST['foreignId']) && intval($_POST['foreignId'])){//删除单条数据
                    $curCartArray = session('cart');
                    foreach ($curCartArray as $k =>$v){
                        if($v['id'] == $_POST['foreignId']  && $v['type'] == $_POST['type']){
                            unset($curCartArray[$k]);
                        }
                    }
                    session('cart',$curCartArray);
                    $this -> ajaxReturn(successMsg('删除成功'));
                }

                if(isset($_POST['cartIds']) && $_POST['cartIds']){
                    //删除整个购物车
                    session('cart',null);
                }
                $this -> ajaxReturn(successMsg('删除成功'));
            }
        }
    }


    
}