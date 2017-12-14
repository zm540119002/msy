<?php
namespace Common\Model;

use Think\Model;
use Think\Model\RelationModel;

class LevelModel extends Model {
    protected $tableName = 'level';
    protected $tablePrefix = '';
    protected $connection = 'DB_CONFIG2';

    protected $_validate = array(
    );

    //新增
    public function addLevel(){
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
    public function saveLevel($where=array()){
        if(!IS_POST){
            return errorMsg(C('NOT_POST'));
        }
        unset($_POST['id']);

        $id = I('post.levelId',0,'int');
        if(!$id){
            return errorMsg('确少参数levelId');
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
    public function delLevel($where=array()){
        if(!IS_POST){
            return errorMsg(C('NOT_POST'));
        }
        unset($_POST['id']);

        $id = I('post.levelId',0,'int');
        if(!$id){
            return errorMsg('确少参数levelId');
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
    public function selectLevel($where=[],$field=[],$join=[]){
        $_where = array(
            'l.status' => 0,
        );
        $_field = array(
            'l.id','l.name','l.settlement_discount','l.fee','l.img','l.detail_img','l.star_img','l.star',
        );
        $_join = array(
        );
        $list = $this
            ->alias('l')
            ->where(array_merge($_where,$where))
            ->field(array_merge($_field,$field))
            ->join(array_merge($_join,$join))
            ->select();
        return $list?:[];
    }
}