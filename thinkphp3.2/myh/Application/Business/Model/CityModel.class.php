<?php
namespace Business\Model;

use Think\Model;
use Think\Model\RelationModel;

class CityModel extends Model {
    protected $tableName = 'city';
    protected $tablePrefix = '';
    protected $connection = 'DB_CONFIG_BUSINESS';

    protected $_validate = array(
    );

    //新增
    public function addCity(){
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
    public function saveCity($where=array()){
        unset($_POST['id']);
        $res = $this->create();
        if(!$res){
            return errorMsg($this->getError());
        }
        $_where = array(
            'status' => 0,
        );
        if(isset($_POST['cityId']) && intval($_POST['cityId'])){
            $id = I('post.cityId',0,'int');
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
    public function delCity($where=array()){
        unset($_POST['id']);
        $_where = array(
            'status' => 0,
        );
        $id = I('post.CityId',0,'int');
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
    public function selectCity($where=[],$field=[],$join=[]){
        $_where = array(
            'ct.status' => 0,
        );
        $_field = array(
            'ct.id','ct.name','ct.type','ct.status','ct.province_id','ct.deposit',
            'ct.partner_fee','ct.agent_fee','ct.partner_id',
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
}