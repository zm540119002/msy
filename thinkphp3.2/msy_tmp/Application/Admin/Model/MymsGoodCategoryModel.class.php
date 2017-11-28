<?php
namespace Admin\Model;

use Think\Model;

class MymsGoodCategoryModel extends Model {
    protected $tableName = 'goods_category';
    protected $tablePrefix = '';
    protected $connection = 'DB_MYMS';

    protected $_validate = array(
        array('name','require','分类名称必须！'),
//        array('name','','分类名称已经存在！',self::EXISTS_VALIDATE,'unique',self::MODEL_INSERT),
    );

    //新增分类
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

    //修改分类
    public function saveGoodsCategory(){
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

        $where = array(
            'id' => $id,
        );
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
    public function delGoodsCategory(){
        if(!IS_POST){
            return errorMsg(C('NOT_POST'));
        }
        unset($_POST['id']);

        $id = I('post.goodsCategoryId',0,'int');
        if(!$id){
            return errorMsg('确少参数goodsCategoryId');
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