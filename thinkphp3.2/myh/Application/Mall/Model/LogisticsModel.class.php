<?php
namespace Mall\Model;

use Think\Model;
use Think\Model\RelationModel;

class LogisticsModel extends Model {
    protected $tableName = 'logistics';
    protected $tablePrefix = '';
    protected $connection = 'DB_CONFIG_MALL';

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
    
  


}