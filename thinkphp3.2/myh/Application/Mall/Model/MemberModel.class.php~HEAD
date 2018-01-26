<?php
namespace Mall\Model;

use Think\Model;
use Think\Model\RelationModel;

class MemberModel extends Model {
    protected $tableName = 'member';
    protected $tablePrefix = '';
    protected $connection = 'DB_CONFIG_MALL';
    protected $_validate = array();

    //新增
    public function addMember($rules=array()){
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
    public function saveMember($where=array(),$rules=array()){
        unset($_POST['id']);
        $this->_validate = array_merge($this->_validate,$rules);

        $_where = array(
        );
        $id = I('post.MemberId',0,'int');
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
    public function delMember($where=array()){
        if(!IS_POST){
            return errorMsg(C('NOT_POST'));
        }
        unset($_POST['id']);
        $_where = array(
        );
        $id = I('post.MemberId',0,'int');
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
    public function selectMember($where=[],$field=[],$join=[]){
        $_where = array(
        );
        $_field = array(
            'm.id','m.user_id','m.qr_code','m.referrer_status'
        );
        $_join = array(
        );
        $list = $this
            ->alias('m')
            ->where(array_merge($_where,$where))
            ->field(array_merge($_field,$field))
            ->join(array_merge($_join,$join))
            ->select();
        return $list?:[];
    }
}