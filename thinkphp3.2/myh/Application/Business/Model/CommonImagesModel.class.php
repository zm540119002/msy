<?php
namespace Common\Model;

use Think\Model;
use Think\Model\RelationModel;

class CommonImagesModel extends Model {
    protected $tableName = 'common_images';
    protected $tablePrefix = '';
    protected $connection = 'DB_CONFIG_BUSINESS';

    protected $_validate = array();

    //新增
    public function addCommonImages($rules=array()){
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
    public function saveCommonImages($where=array(),$rules=array()){
        unset($_POST['id']);
        $this->_validate = array_merge($this->_validate,$rules);

        $_where = array(
        );
        $id = I('post.commonImagesId',0,'int');
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
    public function delCommonImages($where=array()){
        if(!IS_POST){
            return errorMsg(C('NOT_POST'));
        }
        unset($_POST['id']);
        $_where = array(
        );
        $id = I('post.commonImagesId',0,'int');
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
    public function selectCommonImages($where=[],$field=[],$join=[]){
        $_where = array(
        );
        $_field = array(
            'ci.id','ci.common_img',
        );
        $_join = array(
        );
        $list = $this
            ->alias('ci')
            ->where(array_merge($_where,$where))
            ->field(array_merge($_field,$field))
            ->join(array_merge($_join,$join))
            ->select();
        return $list?:[];
    }
}