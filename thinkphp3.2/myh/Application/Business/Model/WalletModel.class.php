<?php
namespace Business\Model;

use Think\Model;
use Think\Model\RelationModel;

class WalletModel extends Model {
    protected $tableName = 'wallet';
    protected $tablePrefix = '';
    protected $connection = 'DB_CONFIG_BUSINESS';

    protected $_validate = array(
    );

    //新增
    public function addWallet(){
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
    public function saveWallet($where=array()){
        $res = $this->create();
        if(!$res){
            return errorMsg($this->getError());
        }
        $_where = array();
        $id = I('post.walletId',0,'int');
        if($id){
            $_where = array(
                'id' => $id,
            );
        }
        $res = $this->where(array_merge($_where,$where))->save();
        if($res === false){
            return errorMsg($this->getError());
        }
        
        return successMsg('修改成功',$res);
    }

    //标记删除
    public function delWallet($where=array()){
        unset($_POST['id']);
        $_where = array();
        $id = I('post.walletId',0,'int');
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
    public function selectWallet($where=[],$field=[],$join=[]){
        $_where = array(
            'w.status' => 0,
        );
        $_field = array(
            'w.id','w.status','w.amount','w.user_id',
        );
        $_join = array(
        );
        $list = $this
            ->alias('w')
            ->where(array_merge($_where,$where))
            ->field(array_merge($_field,$field))
            ->join(array_merge($_join,$join))
            ->select();
        return $list?:[];
    }
}