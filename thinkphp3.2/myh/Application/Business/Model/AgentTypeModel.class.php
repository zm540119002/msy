<?php
namespace Common\Model;

use Think\Model;
use Think\Model\RelationModel;

class AgentTypeModel extends Model {
    protected $tableName = 'agent_type';
    protected $tablePrefix = '';
    protected $connection = 'DB_CONFIG_MALL';

    protected $_validate = array(
    );

    //新增
    public function addAgentType(){
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
    public function saveAgentType($where=array()){
        unset($_POST['id']);
        $res = $this->create();
        if(!$res){
            return errorMsg($this->getError());
        }
        $_where = array(
            'status' => 0,
        );
        if(isset($_POST['agentTypeId']) && intval($_POST['agentTypeId'])){
            $id = I('post.agentTypeId',0,'int');
        }
        if($id){
            $_where['id'] = $id;
        }
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
    public function delAgentType($where=array()){
        unset($_POST['id']);
        $_where = array(
            'status' => 0,
        );
        $id = I('post.agentTypeId',0,'int');
        if($id){
            $_where['id'] = $id;
        }
        $_where = array_merge($_where,$where);
        $res = $this->where($_where)->delete();
        if($res === false){
            return errorMsg($this->getError());
        }
        $returnArray = array(
            'id' => $id,
        );
        return successMsg('删除成功',$returnArray);
    }

    //查询
    public function selectAgentType($where=[],$field=[],$join=[]){
        $_where = array(
            'at.status' => 0,
        );
        $_field = array(
            'at.id','at.type','at.status','at.agent_id',
        );
        $_join = array(
        );
        $list = $this
            ->alias('at')
            ->where(array_merge($_where,$where))
            ->field(array_merge($_field,$field))
            ->join(array_merge($_join,$join))
            ->order('at.id desc')
            ->select();
        return $list?:[];
    }
}