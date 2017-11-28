<?php
namespace Purchase\Model;
use Think\Model;
class GoodsModel extends Model {

    //获取按二级分类的商品列表
    public function getGoodsListByCategoryId($categoryId){

        $parentIds  = M('goods_category') -> where(array('parent_id' =>$categoryId))->field('id,name')->select();
        $goodsList  = array();
        foreach ($parentIds as $k => $v){
            $where['goods_category_id']  = $v['id'];
            $where['_logic']             = 'OR';
            $map['_complex']             = $where;
            $map['state']                = array('eq',1);
            $name                        = $v['name'];
            $goodsList[$name] = M('goods') -> where($map) -> select();

        }
        return $goodsList;
    }

    //获取一级分类的所有商品列表
    public function getCartInfo($categoryId){
        $where['goods_category_id']   = array($categoryId);
        $where['goods_category_id_1'] = array($categoryId);
        $where['_logic']              = 'OR';
        $map['_complex']              = $where;
        $map['state']                 = array('eq',1);
        $goodsList = M('goods')->where($map)->order('id DESC')->select();
        return $goodsList;
    }


  
   //查找某件商品信息
    public function getGoodsInfoByGoodsId($goodsId){
        $where['id'] = $goodsId;
        $goodsInfo = M('goods') -> where($where) -> find();
        return $goodsInfo;
    }


   
}