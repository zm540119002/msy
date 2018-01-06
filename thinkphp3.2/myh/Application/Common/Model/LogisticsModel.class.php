<?php
namespace Common\Model;

use Think\Model;
use Think\Model\RelationModel;

class LogisticsModel extends Model {
    protected $tableName = 'logistics';
    protected $tablePrefix = '';
    protected $connection = 'DB_CONFIG1';

    protected $_validate = array(
        array('sn','require','物流编号必须！'),
    );

    //新增
    public function addLogistics(){
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
    public function saveLogistics($where=array()){
        unset($_POST['id']);
        $res = $this->create();
        if(!$res){
            return errorMsg($this->getError());
        }
        $_where = array(
            'status' => 0,
        );
        if(isset($_POST['LogisticsId']) && intval($_POST['LogisticsId'])){
            $id = I('post.LogisticsId',0,'int');
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
    public function delLogistics($where=array()){
        unset($_POST['id']);
        $_where = array(
            'status' => 0,
        );
        $id = I('post.LogisticsId',0,'int');
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
    public function selectLogistics($where=[],$field=[],$join=[]){
        $_where = array(
            'l.status' => 0,
        );
        $_field = array(
            'l.id','l.sn','l.status','l.undertake_company','l.delivery_time','l.fee',
            'l.create_time','l.finished_time',
        );
        $_join = array(
        );
        $list = $this
            ->alias('l')
            ->where(array_merge($_where,$where))
            ->field(array_merge($_field,$field))
            ->join(array_merge($_join,$join))
            ->Logistics('l.id desc')
            ->select();
        return $list?:[];
    }
    
    //检查订单状态
    public function checkLogisticsStatus($LogisticsInfo){
        if(empty($LogisticsInfo)){
            $res = array(
                'status' => 0,
                'message' => '订单信息有误'
            );
        }elseif($LogisticsInfo['logistics_status'] != '1'){
            $res = array(
                'status' => 0,
                'message' => '订单已支付或已取消',
            );
        }else{
            $res = array(
                'status' => 1,
                'message' => '待支付',
            );
        }
        return $res;
    }

    //按订单状态分组统计
    public function LogisticsStatusCount($where){
        $_where = array(
            'l.status' => 0,
        );
        $_field = array(
            'l.logistics_status','count(1) num',
        );
        $list = $this
            ->alias('o')
            ->where(array_merge($_where,$where))
            ->field($_field)
            ->group('l.logistics_status')
            ->Logistics('l.logistics_status asc')
            ->select();
        $LogisticsStatusCount = array();
        foreach ($list as $value){
            if($value['logistics_status'] != 0){
                $LogisticsStatusCount[$value['logistics_status']] = $value['num'];
            }
        }
        return $LogisticsStatusCount?:[];
    }
}