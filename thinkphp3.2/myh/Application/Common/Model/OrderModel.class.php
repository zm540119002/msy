<?php
namespace Common\Model;

use Think\Model;
use Think\Model\RelationModel;

class OrderModel extends Model {
    protected $tableName = 'orders';
    protected $tablePrefix = '';
    protected $connection = 'DB_CONFIG1';

    protected $_validate = array(
        array('sn','require','订单编号必须！'),
    );

    //新增
    public function addOrder(){
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
    public function saveOrder($where=array()){
        unset($_POST['id']);
        $res = $this->create();
        if(!$res){
            return errorMsg($this->getError());
        }
        $_where = array(
            'status' => 0,
        );
        if(isset($_POST['orderId']) && intval($_POST['orderId'])){
            $id = I('post.orderId',0,'int');
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
    public function delOrder($where=array()){
        unset($_POST['id']);
        $_where = array(
            'status' => 0,
        );
        $id = I('post.orderId',0,'int');
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
    public function selectOrder($where=[],$field=[],$join=[]){
        $_where = array(
            'o.status' => 0,
        );
        $_field = array(
            'o.id','o.sn','o.status','o.logistics_status','o.after_sale_status','o.payment_code',
            'o.amount','o.coupons_pay','o.wallet_pay','o.actually_amount','o.create_time','o.payment_time',
            'o.user_id','o.address_id','o.logistics_id','o.coupons_id','o.finished_time','o.type',
        );
        $_join = array(
        );
        $list = $this
            ->alias('o')
            ->where(array_merge($_where,$where))
            ->field(array_merge($_field,$field))
            ->join(array_merge($_join,$join))
            ->order('o.id desc')
            ->select();
        return $list?:[];
    }
    
    //检查订单状态
    public function checkOrderLogisticsStatus($LogisticsStatus){
        if($LogisticsStatus == 1){
            $res = array(
                'status' => 1,
                'message' => '待支付',
            );
        }elseif($LogisticsStatus == 2 || $LogisticsStatus == 3 || $LogisticsStatus == 4){
            $res = array(
                'status' => 0,
                'message' => '订单已支付',
            );
        }elseif($LogisticsStatus == 5){
            $res = array(
                'status' => 0,
                'message' => '订单已取消',
            );
        }else{
            $res = array(
                'status' => 0,
                'message' => '无效订单',
            );
        }
        return $res;
    }

    //按订单状态分组统计
    public function orderStatusCount($where){
        $_where = array(
            'o.status' => 0,
        );
        $_field = array(
            'o.logistics_status','count(1) num',
        );
        $list = $this
            ->alias('o')
            ->where(array_merge($_where,$where))
            ->field($_field)
            ->group('o.logistics_status')
            ->order('o.logistics_status asc')
            ->select();
        $orderStatusCount = array();
        foreach ($list as $value){
            if($value['logistics_status'] != 0){
                $orderStatusCount[$value['logistics_status']] = $value['num'];
            }
        }
        return $orderStatusCount?:[];
    }
}