<?php
namespace Myms\Controller;
use Think\Controller;
use web\all\Lib\AuthUser;
use web\all\Controller\BaseController;
class CartController extends BaseController {
    //加入购物车
    public function addCart(){
        if(IS_POST){
            $num = isset($_POST['goodsNum']) && intval($_POST['goodsNum']) ? $_POST['goodsNum'] : 1;
            $user = AuthUser::getSession();
            $buyType = intval($_POST['buyType']);
            if(isset($_POST['goodsId']) && !empty($_POST['goodsId'])){//商品添加
                $foreignId = intval($_POST['goodsId']);
                $type = 1;
                $addCartInfo = D('goods')->getGoodsInfoByGoodsId($foreignId,$buyType);

            }
            if(isset($_POST['projectId']) && !empty($_POST['projectId'])){//项目添加
                $foreignId = intval($_POST['projectId']);
                $type = 2;
                $addCartInfo = D('project')->getProjectInfoByProjectId($foreignId,$buyType);
            }
            $addCartInfo['num'] = $num;
            $addCartInfo['type'] = $type;
            $addCartInfo['buy_type'] = $buyType;
            if(isset($_POST['foreignId']) && !empty($_POST['foreignId'])){//购物车修改数量
                $foreignId = intval($_POST['foreignId']);
                $type =  intval($_POST['type']);
            }

            //已登录
            if(isset($user)  && !empty($user)){
                $model = D('cart');
                $userId  = $user['id'];
                $cartInfo = $model -> getCartInfoByForeignId($userId,$foreignId,$buyType,$type);
                if(empty($cartInfo)){//添加
                    $result = $model -> addCart($userId,$addCartInfo);
                    $addCartInfo['cart_id'] = $result;
                    if(!$result){
                        $this -> ajaxReturn(errorMsg('添加购物车失败'));
                    }
                }else{//修改
                    if(isset($_POST['goodsNum'])&& intval($_POST['goodsNum'])){
                        $num = intval($_POST['goodsNum']);
                    }else{
                        $num = $cartInfo['num'] + $num;
                    }

                    $result   = $model-> updateGoodsNum($userId,$foreignId,$num,$buyType,$type);
                    $addCartInfo['cart_id'] = $cartInfo['id'];
                    if(false === $result){
                        $this -> ajaxReturn(errorMsg('更新购物车失败'));
                    }
                }
                //返回购物车的数量和总价
                $cartList = $model -> getCartList($userId);
                $cartIds  = $model -> getCartIds($userId);
                $cartInfo = $model -> getCartInfo($cartList);
                $cartInfo['info'] = $addCartInfo;
                $cartInfo['cartIds'] = $cartIds;
                $this -> ajaxReturn(successMsg($cartInfo));

            }else{ //没有登录
                if(isset($_POST['foreignId']) && intval($_POST['foreignId'])){//修改
                    $curCartArray = session('cart');
                    foreach ($curCartArray as $k =>$v){
                        if(intval($v['id']) == $foreignId && intval($v['buyType']) == $buyType){
                            $curCartArray[$k]['num'] = $_POST['goodsNum'];

                            session('cart',$curCartArray);
                        }
                    }
                }else{//添加
                    $curCartArray = session('cart');
                    if($curCartArray==""){
                        $cartInfo[0]["id"]=$foreignId;//商品id保存到二维数组中
                        $cartInfo[0]["num"]=$num;//商品数量保存到二维数组中
                        $cartInfo[0]["type"]=$type;//商品数量保存到二维数组中
                        $cartInfo[0]["buyType"]=$buyType;//商品数量保存到二维数组中
                        $cartInfo[0]["price"]=$addCartInfo['real_price'];//商品数量保存到二维数组中
                        $cartInfo[0]["main_img"]=$addCartInfo['main_img'];//商品数量保存到二维数组中
                        session('cart',$cartInfo);
//
                    }else {
                        //返回数组键名倒序取最大
                        $ar_keys = array_keys($curCartArray);
                        $len = count($ar_keys);
                        $maxArrayKeyId = $ar_keys[$len - 1] + 1;
                        $is_exist=$this->isExist($foreignId,$num,$buyType,$curCartArray,$type);
                        if($is_exist==false){
                            $curCartArray[$maxArrayKeyId]["id"] = $foreignId;
                            $curCartArray[$maxArrayKeyId]["num"] = $num;
                            $curCartArray[$maxArrayKeyId]["type"] = $type;
                            $curCartArray[$maxArrayKeyId]["buyType"] = $buyType;
                            $curCartArray[$maxArrayKeyId]["price"] = $addCartInfo['real_price'];
                            $curCartArray[$maxArrayKeyId]["main_img"] = $addCartInfo['main_img'];
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
                $cartInfo['info'] = $addCartInfo;
                $this -> ajaxReturn(successMsg($cartInfo));
            }
        }
    }


    //判断购物车是否存在相同商品
    public function isExist($id,$num,$buyType,$array ,$type)
    {
        $isExist=false;
        foreach($array as $key1=>$value)
        {
            if(intval($value['id']) == $id && intval($value['buyType'])==$buyType && intval($value['type'])==$type){
                $num=$value["num"]+$num;
                $isExist=$key1."/".$num;
            }
        }
        return $isExist;
    }

    //购物车信息
    public function myCart(){
        $user = AuthUser::getSession();
        if(isset($user) && !empty($user)){
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
                    $where['user_id']  = $user['id'];
                    $where['id']  = $_POST['cartId'];
                    $result = M('cart') -> where($where)->delete();

                }
                if(isset($_POST['cartIds']) && $_POST['cartIds']){
                    $where['user_id']  = $user['id'];
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
                        if($v['id'] == $_POST['foreignId'] && $v['buyType'] == $_POST['buyType'] && $v['type'] == $_POST['type']){
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