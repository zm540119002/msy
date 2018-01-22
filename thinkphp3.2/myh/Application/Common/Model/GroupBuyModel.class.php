<?php
namespace Common\Model;
use Think\Model;
use Think\Model\RelationModel;
use web\all\Controller\CommonController;
class GroupBuyModel extends Model {
    protected $tableName = 'group_buy';
    protected $tablePrefix = '';
    protected $connection = 'DB_CONFIG_MALL';

    protected $_validate = array();

    //新增
    public function addGroupBuy($rules=array()){
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
    public function saveGroupBuy($where=array(),$rules=array()){
        unset($_POST['id']);
        $this->_validate = array_merge($this->_validate,$rules);
        $_where = array(
            'status' => 0,
        );
        $id = I('post.groupBuyId',0,'int');
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
    public function delGroupBuy($where=array()){
        if(!IS_POST){
            return errorMsg(C('NOT_POST'));
        }
        unset($_POST['id']);
        $_where = array(
            'status' => 0,
        );
        $id = I('post.groupBuyId',0,'int');
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
    public function selectGroupBuy($where=[],$field=[],$join=[]){
        $_where = array(
            'grb.status' => 0,
        );
        $_field = array(
            'grb.id','grb.sn','grb.user_id','grb.goods_id','grb.tag','grb.need_person','grb.create_time',
            'grb.status','grb.overdue_time','grb.tag'
        );
        $_join = array(
        );
        $list = $this
            ->alias('grb')
            ->where(array_merge($_where,$where))
            ->field(array_merge($_field,$field))
            ->join(array_merge($_join,$join))
            ->select();
        return $list?:[];
    }

    //加入团购表
    public function joinGroupBuy($goods,$uid,$orderId,$groupBuyId,$openid){
        //插入团购记录
        if(!$groupBuyId){
            $modelGroupBuy = D('GroupBuy');
            $_POST = [];
            $_POST['goods_id'] = $goods['foreign_id'];
            $_POST['user_id'] = $uid;
            $_POST['create_time'] = time();
            $_POST['sn'] = generateSN();
            $modelGroupBuy->startTrans();//开启事务
            $res = $modelGroupBuy->addGroupBuy();
            $groupBuyId = $res['id'];
            if(!$groupBuyId){
                $modelGroupBuy->rollback();//回滚事务
                $this->ajaxReturn(errorMsg('发起团购失败'));
            }
            $type = 1;//团长
        }else{
            $type = 2;//成员
        }
        $modelGroupBuyDetail = D('GroupBuyDetail');
        $_POST = [];
        $_POST['goods_id'] = $goods['foreign_id'];
        $_POST['num'] = $goods['num'];
        $_POST['price'] = $goods['price'];
        $_POST['user_id'] =$uid;
        $_POST['order_id'] = $orderId;
        $_POST['group_buy_id'] = $groupBuyId;
        $_POST['type'] = $type;
        $_POST['openid'] = $openid;
        $res = $modelGroupBuyDetail->addGroupBuyDetail();
        $groupBuyDetailId = $res['id'];
        if(!$groupBuyDetailId){
            $modelGroupBuy->rollback();//回滚事务
            $this->ajaxReturn(errorMsg('发起团购失败'));
        }
    }


}