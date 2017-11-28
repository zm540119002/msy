<?php
namespace Myms\Model;

use Think\Model;

class GoodsModel extends Model {
    protected $tableName = 'goods';
    protected $tablePrefix = '';

    //查询商品
    public function selectGoods($where=[],$field=[],$join=[],$order=[],$limit=''){
        $_where = array(
            'g.status' => 0,
        );
        $_field = array(
            'g.id','g.no','g.name','g.status','g.category_id_1','g.category_id_2','g.category_id_3','g.param','g.on_off_line',
            'g.sort','g.specification','g.price','g.special_price','g.group_price','g.inventory','g.main_img','g.detail_img','g.create_time',
            'g.intro','g.tag','g.discount_price',
        );
        $_join = array(
            
        );
        $_order = array(
            'g.id desc'
        );
        $list = $this
            ->alias('g')
            ->where(array_merge($_where,$where))
            ->field(array_merge($_field,$field))
            ->join(array_merge($_join,$join))
            ->order(array_merge($_order,$order))
            ->limit($limit)
            ->select();
        return $list?:[];
    }

    //查找某件商品信息
    public function getGoodsInfoByGoodsId($goodsId,$buyType){
        $where['id'] = $goodsId;
        $goodsInfo = M('goods') -> where($where) -> find();
        $goodsInfo['buy_type'] = $buyType;
        if($buyType=='1'){
            $goodsInfo['real_price'] = $goodsInfo['discount_price'];
        }
        if($buyType=='3'){
            $goodsInfo['real_price'] = $goodsInfo['group_price'];
        }
        if($buyType=='2'){
            $goodsInfo['real_price'] = $goodsInfo['special_price'];
        }

        return $goodsInfo;
    }
    



}