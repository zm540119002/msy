<?php
namespace Common\Model;

use Think\Model;
use Think\Model\RelationModel;

class CompanyModel extends Model {
    protected $tableName = 'company';
    protected $tablePrefix = '';
    protected $connection = 'DB_CONFIG1';

    protected $_validate = array();

    //新增
    public function addCompany($rules=array()){
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
    public function saveCompany($where=array(),$rules=array()){
        unset($_POST['id']);
        $this->_validate = array_merge($this->_validate,$rules);

        $_where = array(
            'status' => 0,
        );
        $id = I('post.companyId',0,'int');
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
    public function delCompany($where=array()){
        if(!IS_POST){
            return errorMsg(C('NOT_POST'));
        }
        unset($_POST['id']);
        $_where = array(
            'status' => 0,
        );
        $id = I('post.companyId',0,'int');
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
    public function selectCompany($where=[],$field=[],$join=[]){
        $_where = array(
            'c.status' => 0,
        );
        $_field = array(
            'c.id','c.name','c.shorten_name','c.logo','c.scale','c.type','c.status','c.auth_status',
            'c.father_id','c.user_id','c.telephone','c.level','c.registrant','c.registrant_mobile',
            'c.license_url','c.id_url','c.id_reverse_url','c.unit_certificate_url','c.create_time',
            'c.figure_url_0','c.figure_url_1','c.figure_url_2','c.figure_url_3',
            'c.figure_url_4','c.figure_url_5','c.figure_url_6','c.figure_url_7',
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