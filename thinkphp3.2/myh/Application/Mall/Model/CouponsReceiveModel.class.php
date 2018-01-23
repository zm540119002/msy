<?php
namespace Mall\Model;

use Think\Model;
use Think\Model\RelationModel;

class CouponsReceiveModel extends Model {
    protected $tableName = 'coupons_receive';
    protected $tablePrefix = '';
    protected $connection = 'DB_CONFIG_MALL';

    protected $_validate = array(
        array('sn','require','订单编号必须！'),
    );

    //新增
    public function addCouponsReceive(){
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
    public function saveCouponsReceive($where=array()){
        if(!IS_POST){
            return errorMsg(C('NOT_POST'));
        }
        unset($_POST['id']);

        $id = I('post.couponsId',0,'int');
        if(!$id){
            return errorMsg('确少参数couponsId');
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
    public function delCouponsReceive($where=array()){
        if(!IS_POST){
            return errorMsg(C('NOT_POST'));
        }
        unset($_POST['id']);

        $id = I('post.couponsId',0,'int');
        if(!$id){
            return errorMsg('确少参数couponsId');
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
    public function selectCouponsReceive($where=[],$field=[],$join=[]){
        $_where = array(
            'cr.status' => 0,
        );
        $_field = array(
            'cr.id','cr.user_id','cr.coupons_id','cr.type','cr.type','cr.amount','cr.failure_time','cr.acquire_time'
        );
        $_join = array(
        );
        $list = $this
            ->alias('cr')
            ->where(array_merge($_where,$where))
            ->field(array_merge($_field,$field))
            ->join(array_merge($_join,$join))
            ->select();
        return $list?:[];
    }
}