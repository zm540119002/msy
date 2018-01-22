<?php
namespace Common\Model;

use Think\Model;
use Think\Model\RelationModel;

class PartnerModel extends Model {
    protected $tableName = 'partner';
    protected $tablePrefix = '';
    protected $connection = 'DB_CONFIG_BUSINESS';

    protected $_validate = array(
    );

    //新增
    public function addPartner(){
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
    public function savePartner($where=array()){
        unset($_POST['id']);
        $res = $this->create();
        if(!$res){
            return errorMsg($this->getError());
        }
        $_where = array(
            'status' => 0,
        );
        if(isset($_POST['partnerId']) && intval($_POST['partnerId'])){
            $id = I('post.partnerId',0,'int');
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
    public function delPartner($where=array()){
        unset($_POST['id']);
        $_where = array(
            'status' => 0,
        );
        $id = I('post.partnerId',0,'int');
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
    public function selectPartner($where=[],$field=[],$join=[]){
        $_where = array(
            'p.status' => 0,
        );
        $_field = array(
            'p.id','p.name','p.status','p.mobile_phone','p.registrant','p.auth_status','p.province','p.city',
            'p.user_id','p.create_time','p.agent_places','p.authorized_agent','p.telephone','p.detail_address',
        );
        $_join = array(
        );
        $list = $this
            ->alias('p')
            ->where(array_merge($_where,$where))
            ->field(array_merge($_field,$field))
            ->join(array_merge($_join,$join))
            ->order('p.id desc')
            ->select();
        return $list?:[];
    }
}