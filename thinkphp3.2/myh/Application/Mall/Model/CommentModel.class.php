<?php
namespace Mall\Model;

use Think\Model;
use Think\Model\RelationModel;

class CommentModel extends Model {
    protected $tableName = 'comment';
    protected $tablePrefix = '';
    protected $connection = 'DB_CONFIG_MALL';

    protected $_validate = array(
    );

    //新增
    public function addComment(){
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
    public function saveComment($where=array()){
        unset($_POST['id']);
        $res = $this->create();
        if(!$res){
            return errorMsg($this->getError());
        }
        $_where = array(
            'status' => 0,
        );
        if(isset($_POST['CommentId']) && intval($_POST['CommentId'])){
            $id = I('post.CommentId',0,'int');
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
    public function delComment($where=array()){
        unset($_POST['id']);
        $_where = array(
            'status' => 0,
        );
        $id = I('post.CommentId',0,'int');
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
    public function selectComment($where=[],$field=[],$join=[]){
        $_where = array(
            'cm.status' => 0,
        );
       
        $_field = array(
           'cm.id','cm.user_id','cm.score','cm.order_id','cm.title','cm.content','cm.create_time','cm.update_time'
        );
        $_join = array(
        );
        $list = $this
            ->alias('cm')
            ->where(array_merge($_where,$where))
            ->field(array_merge($_field,$field))
            ->join(array_merge($_join,$join))
            ->select();
        return $list?:[];
    }
}