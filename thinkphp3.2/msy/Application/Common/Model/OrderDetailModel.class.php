<?php
namespace Common\Model;

use Think\Model;
use Think\Model\RelationModel;

class OrderDetailModel extends Model {
    protected $tableName = 'order_detail';
    protected $tablePrefix = '';
    protected $connection = 'DB_CONFIG1';

    protected $_validate = array(
        array('sn','require','订单编号必须！'),
    );

    //新增
    public function addOrderDetail(){
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
    public function saveOrderDetail(){
        if(!IS_POST){
            return errorMsg(C('NOT_POST'));
        }
        unset($_POST['id']);

        $id = I('post.orderDetailId',0,'int');
        if(!$id){
            return errorMsg('确少参数orderDetailId');
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

    //标记删除
    public function delOrderDetail($where=array()){
        if(!IS_POST){
            return errorMsg(C('NOT_POST'));
        }
        unset($_POST['id']);

        $id = I('post.orderDetailId',0,'int');
        if(!$id){
            return errorMsg('确少参数orderDetailId');
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
    public function selectOrderDetail($where=[],$field=[],$join=[]){
        $_where = array(
            'od.status' => 0,
        );
        $_field = array(
            'od.id','od.order_sn','od.type','od.status','od.price','od.num','od.foreign_id','od.user_id',
        );
        $_join = array(
        );
        $list = $this
            ->alias('od')
            ->where(array_merge($_where,$where))
            ->field(array_merge($_field,$field))
            ->join(array_merge($_join,$join))
            ->select();
        return $list?:[];
    }
}