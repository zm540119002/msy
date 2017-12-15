<?php
namespace Common\Model;

use Think\Model;
use Think\Model\RelationModel;

class WalletDetailModel extends Model {
    protected $tableName = 'wallet_detail';
    protected $tablePrefix = '';
    protected $connection = 'DB_CONFIG1';

    protected $_validate = array(
    );

    //新增
    public function addWalletDetail(){
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
    public function saveWalletDetail($where=array()){

        unset($_POST['id']);
        $res = $this->create();
        if(!$res){
            return errorMsg($this->getError());
        }
        $_where = array();
        $id = I('post.walletDetailId',0,'int');
        if($id){
            $_where = array(
                'id' => $id,
            );
        }
        $res = $this->where(array_merge($_where,$where))->save();
        if($res === false){
            return errorMsg($this->getError());
        }
        $returnArray = array(
            'id' => $id,
        );

        return successMsg('修改成功',$returnArray);
    }

    //标记删除
    public function delWalletDetail($where=array()){
        unset($_POST['id']);

        $_where = array();
        $id = I('post.walletDetailId',0,'int');
        if($id){
            $_where = array(
                'id' => $id,
            );
        }
        $res = $this->where(array_merge($_where,$where))->setField('status',2);
        if($res === false){
            return errorMsg($this->getError());
        }

        $returnArray = array(
            'id' => $id,
        );

        return successMsg('删除成功',$returnArray);
    }

    //查询
    public function selectWalletDetail($where=[],$field=[],$join=[]){
        $_where = array(
            'wd.status' => 0,
        );
        $_field = array(
            'wd.id','wd.type','wd.amount','wd.create_time','wd.user_id',
            'wd.sn','wd.recharge_status','payment_code','wd.payment_code'
        );
        $_join = array(
        );
        $list = $this
            ->alias('wd')
            ->where(array_merge($_where,$where))
            ->field(array_merge($_field,$field))
            ->join(array_merge($_join,$join))
            ->select();
        return $list?:[];
    }
}