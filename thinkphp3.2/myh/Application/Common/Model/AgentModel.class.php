<?php
namespace Common\Model;

use Think\Model;
use Think\Model\RelationModel;

class AgentModel extends Model {
    protected $tableName = 'agent';
    protected $tablePrefix = '';
    protected $connection = 'DB_CONFIG1';

    protected $_validate = array(
    );

    //新增
    public function addAgent(){
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
    public function saveAgent($where=array()){
        unset($_POST['id']);
        $res = $this->create();
        if(!$res){
            return errorMsg($this->getError());
        }
        $_where = array(
            'status' => 0,
        );
        if(isset($_POST['agentId']) && intval($_POST['agentId'])){
            $id = I('post.agentId',0,'int');
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
    public function delAgent($where=array()){
        unset($_POST['id']);
        $_where = array(
            'status' => 0,
        );
        $id = I('post.agentId',0,'int');
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
    public function selectAgent($where=[],$field=[],$join=[]){
        $_where = array(
            'a.status' => 0,
        );
        $_field = array(
            'a.id','a.name','a.status','a.mobile_phone','a.company_name','a.auth_status','a.province','a.city',
            'a.partner_id','a.user_id','a.create_time',
        );
        $_join = array(
        );
        $list = $this
            ->alias('a')
            ->where(array_merge($_where,$where))
            ->field(array_merge($_field,$field))
            ->join(array_merge($_join,$join))
            ->order('a.id desc')
            ->select();
        return $list?:[];
    }
}