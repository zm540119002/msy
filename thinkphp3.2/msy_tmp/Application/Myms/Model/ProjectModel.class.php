<?php
namespace Myms\Model;

use Think\Model;

class ProjectModel extends Model {
    protected $tableName = 'project';
    protected $tablePrefix = '';

    //查询商品
    public function selectProject($where=[],$field=[],$join=[],$order=[],$limit=''){
        $_where = array(
            'p.status' => 0,
        );
        $_field = array(
            'p.id','p.no','p.name','p.status','p.category_id_1','p.category_id_2','p.category_id_3','p.on_off_line',
            'p.sort','p.price','p.group_price','p.main_img','p.detail_img','p.create_time','p.intro','p.explain_img',
            'p.flow_img','p.consume_time','p.discount_price',
        );
        $_join = array(
            
        );
        $_order = array(

        );
        $list = $this
            ->alias('p')
            ->where(array_merge($_where,$where))
            ->field(array_merge($_field,$field))
            ->join(array_merge($_join,$join))
            ->order(array_merge($_order,$order))
            ->limit($limit)
            ->select();
        return $list?:[];
    }

    //查找某件商品信息
    public function getProjectInfoByProjectId($projectId,$buyType){
        $where['id'] = $projectId;
        $projectInfo = M('project') -> where($where) -> find();
        $projectInfo['buy_type'] = $buyType;
        if($buyType == '3'){
            $projectInfo['real_price'] = $projectInfo['group_price'];
        }
        if($buyType == '1'){
            $projectInfo['real_price'] = $projectInfo['discount_price'];
        }

        return $projectInfo;
    }


    //查询分类
    public function selectProjectCategory($where=[],$field=[],$join=[]){
        $_where = array(
            'pc.status' => 0,
        );
        $_field = array(
            'pc.id','pc.name','pc.status','pc.level','pc.parent_id_1','pc.parent_id_2','pc.sort','pc.explain','pc.img',
        );
        $_join = array(
        );
        $list = M('project_goods')
            ->alias('pc')
            ->where(array_merge($_where,$where))
            ->field(array_merge($_field,$field))
            ->join(array_merge($_join,$join))
            ->select();
        return $list?:[];
    }
    



}