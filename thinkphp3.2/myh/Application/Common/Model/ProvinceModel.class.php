<?php
namespace Common\Model;

use Think\Model;
use Think\Model\RelationModel;

class ProvinceModel extends Model {
    protected $tableName = 'province';
    protected $tablePrefix = '';
    protected $connection = 'DB_CONFIG_MALL';

    protected $_validate = array(
    );

    //新增
    public function addProvince(){
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
    public function saveProvince($where=array()){
        unset($_POST['id']);
        $res = $this->create();
        if(!$res){
            return errorMsg($this->getError());
        }
        $_where = array(
            'status' => 0,
        );
        if(isset($_POST['provinceId']) && intval($_POST['provinceId'])){
            $id = I('post.provinceId',0,'int');
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
    public function delProvince($where=array()){
        unset($_POST['id']);
        $_where = array(
            'status' => 0,
        );
        $id = I('post.ProvinceId',0,'int');
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
    public function selectProvince($where=[],$field=[],$join=[]){
        $_where = array(
            'pv.status' => 0,
        );
       
        $_field = array(
           'pv.id','pv.name','pv.type','pv.status',
        );
        $_join = array(
        );
        $list = $this
            ->alias('pv')
            ->where(array_merge($_where,$where))
            ->field(array_merge($_field,$field))
            ->join(array_merge($_join,$join))
            ->select();
        return $list?:[];
    }
}