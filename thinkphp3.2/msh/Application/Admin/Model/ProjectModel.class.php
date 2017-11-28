<?php
namespace Admin\Model;

use Think\Model;
use Think\Model\RelationModel;

class ProjectModel extends Model {
    protected $tableName = 'project';
    protected $tablePrefix = '';
    protected $connection = 'DB_MYMS';

    protected $_validate = array(
        array('category_id_1','require','所属分类必须！'),
        array('name','require','项目名称必须！'),
        array('price','require','项目原价必须！'),
//        array('name','','分类名称已经存在！',self::EXISTS_VALIDATE,'unique',self::MODEL_BOTH),
    );

    //新增分类
    public function addProject(){
        if(!IS_POST){
            return errorMsg(C('NOT_POST'));
        }
        unset($_POST['id']);

        $res = $this->create();
        if(!$res){
            return errorMsg($this->getError());
        }
        $_POST['create_time'] = time();

        $id = $this->add();
        if($id === false){
            return errorMsg($this->getError());
        }

        $returnArray = array(
            'id' => $id,
        );

        return successMsg('新增成功',$returnArray);
    }

    //修改分类
    public function saveProject(){
        if(!IS_POST){
            return errorMsg(C('NOT_POST'));
        }
        unset($_POST['id']);

        $id = I('post.projectId',0,'int');
        if(!$id){
            return errorMsg('确少参数projectId');
        }

        $res = $this->create();
        if(!$res){
            return errorMsg($this->getError());
        }

        $where = array(
            'id' => $id,
        );
        $_POST['update_time'] = time();
        $res = $this->where($where)->save();
        if($res === false){
            return errorMsg($this->getError());
        }

        $returnArray = array(
            'id' => $id,
        );

        return successMsg('修改成功',$returnArray);
    }

    //删除分类
    public function delProject(){
        if(!IS_POST){
            return errorMsg(C('NOT_POST'));
        }
        unset($_POST['id']);

        $id = I('post.projectId',0,'int');
        if(!$id){
            return errorMsg('确少参数projectId');
        }

        $where = array(
            'id' => $id,
        );

        $res = $this->where($where)->setField('status',2);
        if($res === false){
            return errorMsg($this->getError());
        }

        $returnArray = array(
            'id' => $id,
        );

        return successMsg('删除成功',$returnArray);
    }

    //查询分类
    public function selectProject($where=[],$field=[],$join=[]){
        $_where = array(
            'p.status' => 0,
        );
        $_field = array(
            'p.id','p.no','p.name','p.status','p.category_id_1','p.category_id_2','p.category_id_3','p.on_off_line',
            'p.sort','p.price','p.group_price','p.discount_price','p.main_img','p.detail_img','p.create_time','p.intro','p.explain_img',
            'p.flow_img','p.consume_time','p.inventory'
        );
        $_join = array(
        );
        $list = $this
            ->alias('p')
            ->where(array_merge($_where,$where))
            ->field(array_merge($_field,$field))
            ->join(array_merge($_join,$join))
            ->select();
        return $list?:[];
    }
  //查询项目产品
    public function selectProjectGoods($where=[],$field=[],$join=[]){
        $_where = array(

        );
        $_field = array(
            'pg.id','pg.project_id','pg.goods_id','pg.goods_num'
        );
        $_join = array(
        );
        $list =  M('project_goods','','DB_MYMS')
            ->alias('pg')
            ->where(array_merge($_where,$where))
            ->field(array_merge($_field,$field))
            ->join(array_merge($_join,$join))
            ->select();
        return $list?:[];
    }

    //修改项目产品数量
    public function updateProjectGoodsNum($goodsId,$goodsNum){
        $where['goods_id'] = $goodsId;
        $result=  M('project_goods','','DB_MYMS') -> where($where)->setField('goods_num',$goodsNum);
        return $result;
    }

 
}