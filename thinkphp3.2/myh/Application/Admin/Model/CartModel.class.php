<?php
namespace Common\Model;

use Think\Model;
use Think\Model\RelationModel;

class CartModel extends Model {
    protected $tableName = 'cart';
    protected $tablePrefix = '';
    protected $connection = 'DB_CONFIG_MALL';

    protected $_validate = array();

    //新增
    public function addCart($rules=array()){
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
    public function saveCart($where=array(),$rules=array()){
        unset($_POST['id']);
        $this->_validate = array_merge($this->_validate,$rules);

        $_where = array(
            'status' => 0,
        );
        $id = I('post.cartId',0,'int');
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
    public function delCart($where=array()){
        if(!IS_POST){
            return errorMsg(C('NOT_POST'));
        }
        unset($_POST['id']);
        $_where = array(
            'status' => 0,
        );
        $id = I('post.cartId',0,'int');
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
    public function selectCart($where=[],$field=[],$join=[]){
        $_where = array(
            'ct.status' => 0,
        );
        $_field = array(
            'ct.id','ct.user_id','ct.type','ct.status','ct.foreign_id','ct.num','ct.create_time',
        );
        $_join = array(
        );
        $list = $this
            ->alias('ct')
            ->where(array_merge($_where,$where))
            ->field(array_merge($_field,$field))
            ->join(array_merge($_join,$join))
            ->select();
        return $list?:[];
    }

    //购物车统计
    public function cartCount($where){
        $_where = array(
            'ct.status' => 0,
        );
        $_field = array(
            'ct.type','count(1) num',
        );
        $list = $this
            ->alias('ct')
            ->where(array_merge($_where,$where))
            ->field($_field)
            ->group('ct.type')
            ->select();
        $cartCount = array();
        foreach ($list as $value){
            $cartCount[$value['type']] = $value['num'];
        }
        return $cartCount?:[];
    }
}