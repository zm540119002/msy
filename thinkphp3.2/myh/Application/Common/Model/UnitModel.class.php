<?php
namespace Common\Model;

use Think\Model;
use Think\Model\RelationModel;

class UnitModel extends Model {
    protected $tableName = 'unit';
    protected $tablePrefix = '';
    protected $connection = 'DB_CONFIG1';

    protected $_validate = array();

    //新增
    public function addUnit($rules=array()){
        unset($_POST['id']);
        $this->_validate = array_merge($this->_validate,$rules);

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
    public function saveUnit($where=array(),$rules=array()){
        unset($_POST['id']);
        $this->_validate = array_merge($this->_validate,$rules);

        $_where = array(
            'status' => 0,
        );
        $id = I('post.unitId',0,'int');
        if($id){
            $_where['id'] = $id;
        }
        $_where = array_merge($_where,$where);

        $res = $this->create();
        if(!$res){
            return errorMsg($this->getError());
        }
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
    public function delUnit($where=array()){
        if(!IS_POST){
            return errorMsg(C('NOT_POST'));
        }
        unset($_POST['id']);
        $_where = array(
            'status' => 0,
        );
        $id = I('post.unitId',0,'int');
        if($id){
            $_where['id'] = $id;
        }
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
    public function selectUnit($where=[],$field=[],$join=[],$order=[]){
        $_where = array(
            'ut.status' => 0,
        );
        $_field = array(
            'ut.id','ut.key','ut.value','ut.status',
        );
        $_join = array(
        );
        $_order = array(
            'ut.key','ut.id',
        );
        $list = $this
            ->alias('ut')
            ->where(array_merge($_where,$where))
            ->field(array_merge($_field,$field))
            ->join(array_merge($_join,$join))
            ->order(array_merge($_order,$order))
            ->select();
        return $list?:[];
    }
}