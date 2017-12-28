<?php
namespace Common\Model;

use Think\Model;
use Think\Model\RelationModel;

class CommentModel extends Model {
    protected $tableName = 'comment';
    protected $tablePrefix = '';
    protected $connection = 'DB_CONFIG1';

    protected $_validate = array(
        array('sn','require','订单编号必须！'),
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
            'o.status' => 0,
        );
       
        $_field = array(
           'c.id','c.user_id','c.score','c.order_id','c.title','c.content','c.create_time','c.update_time'
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
    
    //检查订单状态
    public function checkCommentStatus($CommentInfo){
        if(empty($CommentInfo)){
            $res = array(
                'status' => 0,
                'message' => '订单信息有误'
            );
        }elseif($CommentInfo['logistics_status'] != '1'){
            $res = array(
                'status' => 0,
                'message' => '订单已支付或已取消'
            );
        }else{
            $res = array(
                'status' => 1,
                'message' => '待支付'
            );
        }
        return $res;
    }
}