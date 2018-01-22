<?php
namespace Common\Model;

use Think\Model;
use Think\Model\RelationModel;

class ConsigneeAddressModel extends Model {
    protected $tableName = 'consignee_address';
    protected $tablePrefix = '';
    protected $connection = 'DB_CONFIG_BUSINESS';

    protected $_validate = array(
        array('consignee','require','收货人姓名必须！'),
        array('consignee_mobile','require','收货人手机号码必须！'),
        array('consignee_mobile','isMobile','请输入正确的手机号码',0,'function'),
    );

    //新增
    public function addConsigneeAddress($rules=array()){
        if(!IS_POST){
            return errorMsg(C('NOT_POST'));
        }
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
    public function saveConsigneeAddress($where=array(),$rules=array()){
        if(!IS_POST){
            return errorMsg(C('NOT_POST'));
        }
        unset($_POST['id']);
        $this->_validate = array_merge($this->_validate,$rules);
        $_where = array(
            'status' => 0,
        );
        $id = I('post.consigneeAddressId',0,'int');
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
    public function delConsigneeAddress($where=array()){
        if(!IS_POST){
            return errorMsg(C('NOT_POST'));
        }
        unset($_POST['id']);
        $_where = array(
            'status' => 0,
        );
        $id = I('post.consigneeAddressId',0,'int');
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
    public function selectConsigneeAddress($where=[],$field=[],$join=[]){
        $_where = array(
            'ca.status' => 0,
        );
        $_field = array(
            'ca.id','ca.type','ca.status','ca.user_id','ca.province','ca.city','ca.area',
            'ca.detailed_address','ca.consignee_name','ca.consignee_mobile',
        );
        $_join = array(
        );
        $list = $this
            ->alias('ca')
            ->where(array_merge($_where,$where))
            ->field(array_merge($_field,$field))
            ->join(array_merge($_join,$join))
            ->select();
        return $list?:[];
    }

    //设置type为0
    public function setTypeZeroByUserId($userId){
        if(!IS_POST){
            return errorMsg(C('NOT_POST'));
        }
        if(!$userId){
            return errorMsg('缺少参数userId');
        }
        $res = $this->where(array('user_id'=>$userId))->setField('type',0);
        if($res === false){
            return errorMsg($this->getError());
        }
        return successMsg('成功');
    }
}