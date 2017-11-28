<?php
namespace Common\Model;

use Think\Model;

class GoodsCategoryModel extends Model {
    protected $tableName = 'goods_category';
    protected $tablePrefix = '';
    protected $connection = 'DB_CONFIG1';

    protected $_validate = array(
        array('name','require','名称必须！'),
    );

    //新增
    public function addGoodsCategory(){
        if(!IS_POST){
            return errorMsg(C('NOT_POST'));
        }
        unset($_POST['id']);

        $res = $this->create();
        if(!$res){
            return errorMsg($this->getError());
        }

        $id = $this->add();
        if($id === false){
            return errorMsg($this->getError());
        }

        $returnArray = array(
            'id' => $id,
        );

        return successMsg('新增成功',$returnArray);
    }

    //修改
    public function saveGoodsCategory($where=array()){
        if(!IS_POST){
            return errorMsg(C('NOT_POST'));
        }
        unset($_POST['id']);

        $id = I('post.goodsCategoryId',0,'int');
        if(!$id){
            return errorMsg('确少参数goodsCategoryId');
        }

        $res = $this->create();
        if(!$res){
            return errorMsg($this->getError());
        }

        $_where = array(
            'id' => $id,
        );
        $_where = array_merge($_where,$where);

        $res = $this->where($_where)->save();
        if($res === false){
            return errorMsg($this->getError());
        }

        $returnArray = array(
            'id' => $id,
        );

        return successMsg('修改成功',$returnArray);
    }

    //标记删除
    public function delGoodsCategory($where=array()){
        if(!IS_POST){
            return errorMsg(C('NOT_POST'));
        }
        unset($_POST['id']);

        $id = I('post.goodsCategoryId',0,'int');
        if(!$id){
            return errorMsg('确少参数goodsCategoryId');
        }

        $_where = array(
            'id' => $id,
        );
        $_where = array_merge($_where,$where);

        $res = $this->where($_where)->setField('status',2);
        if($res === false){
            return errorMsg($this->getError());
        }

        $returnArray = array(
            'id' => $id,
        );

        return successMsg('删除成功',$returnArray);
    }

    //查询
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
            ->order('gc.sort asc')
            ->select();
        return $list?:[];
    }
}