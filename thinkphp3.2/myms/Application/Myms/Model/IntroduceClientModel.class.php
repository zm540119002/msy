<?php
namespace Myms\Model;
use Think\Model;
class IntroduceClientModel extends Model {

    protected $tableName = 'coupons';
    protected $tablePrefix = '';
    protected $connection = 'DB_MYMS';

    protected $_validate = array(

    );

    //新增
    public function addCoupons(){
        if(!IS_POST){
            return errorMsg(C('NOT_POST'));
        }
        unset($_POST['id']);

        $res = $this->create();
        if(!$res){
            return errorMsg($this->getError());
        }
        $_POST['failure_time'] = strtotime($_POST['failure_time']);
        $id = $this->add($_POST);

        if($id === false){
            return errorMsg($this->getError());
        }

        $returnArray = array(
            'id' => $id,
        );

        return successMsg('新增成功',$returnArray);
    }

    //修改
    public function saveCoupons($where=array()){
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
        $_POST['failure_time'] = strtotime($_POST['failure_time']);
        $res = $this->where($_where)->save($_POST);
        if($res === false){
            return errorMsg($this->getError());
        }

        $returnArray = array(
            'id' => $id,
        );

        return successMsg('修改成功',$returnArray);
    }

    //标记删除
    public function delCoupons($where=array()){
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
    public function selectCoupons($where=[],$field=[],$join=[]){
        $_where = array(
            'c.status' => 0,
        );
        $_field = array(
            'c.id','c.name','c.status','c.type','c.amount','c.failure_time',
        );
        $_join = array(
        );
        $list = $this
            ->alias('c')
            ->where(array_merge($_where,$where))
            ->field(array_merge($_field,$field))
            ->join(array_merge($_join,$join))
            ->select();
        return $list?:[];
    }

        

}