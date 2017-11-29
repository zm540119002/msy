<?php
namespace Home\Model;
use Think\Model;
class ProjectCategoryModel extends Model {
    protected $tableName = 'project_category';
    protected $tablePrefix = '';
    
    //查询分类
    public function selectProjectCategory($where=[],$field=[],$join=[]){
        $_where = array(
            'pc.status' => 0,
        );
        $_field = array(
            'pc.id','pc.name','pc.status','pc.level','pc.parent_id_1','pc.parent_id_2','pc.sort','pc.explain','pc.img'
        );
        $_join = array(
        );
        $list = $this
            ->alias('pc')
            ->where(array_merge($_where,$where))
            ->field(array_merge($_field,$field))
            ->join(array_merge($_join,$join))
            ->select();
        return $list?:[];
    }
}