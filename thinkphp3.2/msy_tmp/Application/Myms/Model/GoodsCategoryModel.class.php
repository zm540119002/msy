<?php
namespace Myms\Model;

use Think\Model;

class GoodsCategoryModel extends Model {
    protected $tableName = 'goods_category';
    protected $tablePrefix = '';
    
    //查询分类
    public function selectGoodsCategory($where=[],$field=[],$join=[]){
        $_where = array(
            'gc.status' => 0,
        );
        $_field = array(
            'gc.id','gc.name','gc.status','gc.level','gc.parent_id_1','gc.parent_id_2','gc.sort','gc.explain','gc.img'
        );
        $_join = array(
        );
        $list = $this
            ->alias('gc')
            ->where(array_merge($_where,$where))
            ->field(array_merge($_field,$field))
            ->join(array_merge($_join,$join))
            ->select();
        return $list?:[];
    }
}