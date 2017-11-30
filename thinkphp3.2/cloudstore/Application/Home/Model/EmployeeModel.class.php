<?php
namespace Home\Model;

use Think\Model;
use Think\Model\RelationModel;

class EmployeeModel extends Model {
    protected $tableName = 'employee';
    protected $tablePrefix = '';
    protected $connection = 'DB_CLOUD_STORE';

    protected $_validate = array(
//        array('category_id_1','require','所属分类必须！'),
//        array('name','require','商品名称必须！'),
//        array('price','require','商品原价必须！'),
//        array('name','','分类名称已经存在！',self::EXISTS_VALIDATE,'unique',self::MODEL_BOTH),
    );

    //新增分类
    public function addEmployee(){
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

    //修改分类
    public function saveEmployee(){
        if(!IS_POST){
            return errorMsg(C('NOT_POST'));
        }
        unset($_POST['id']);

        $id = I('post.employeeId',0,'int');
        if(!$id){
            return errorMsg('确少参数employeeId');
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

    //删除分类
    public function delEmployee(){
        if(!IS_POST){
            return errorMsg(C('NOT_POST'));
        }
        unset($_POST['id']);

        $id = I('post.employeeId',0,'int');
        if(!$id){
            return errorMsg('确少参数employeeId');
        }

        $where = array(
            'id' => $id,
        );

        $res = $this->where($where)->setField('status',2);
        if($res === false){
            return errorMsg($this->getError());
        }

        $returnArray = array(
            'id' => $id,
        );

        return successMsg('删除成功',$returnArray);
    }

    //查询门店
    protected function selectEmployee($where=[],$field=[],$join=[]){
        $_where = array(
            'e.status' => 0,
        );
        $_field = array(
            'e.id','e.name','e.nickname','e.user_id','e.company_id','e.shop_id','e.position_id','e.mobile_phone',
        );
        $_join = array(
        );
        $list = $this
            ->alias('e')
            ->where(array_merge($_where,$where))
            ->field(array_merge($_field,$field))
            ->join(array_merge($_join,$join))
            ->select();
        return $list?:[];
    }
}