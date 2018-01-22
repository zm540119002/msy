<?php
namespace Common\Model;

use Think\Model;
use Think\Model\RelationModel;

class GoodsModel extends Model {
    protected $tableName = 'goods';
    protected $tablePrefix = '';
    protected $connection = 'DB_CONFIG_MALL';

    protected $_validate = array(
        array('price','require','商品原价必须！'),
    );

    //新增
    public function addGoods(){
        if(!IS_POST){
            return errorMsg(C('NOT_POST'));
        }
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
    public function saveGoods($where=array()){
        if(!IS_POST){
            return errorMsg(C('NOT_POST'));
        }
        unset($_POST['id']);

        $id = I('post.goodsId',0,'int');
        if(!$id){
            return errorMsg('确少参数goodsId');
        }

        $res = $this->create();
        if(!$res){
            return errorMsg($this->getError());
        }

        $_where = array(
            'id' => $id,
        );
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
    public function delGoods($where=array()){
        if(!IS_POST){
            return errorMsg(C('NOT_POST'));
        }
        unset($_POST['id']);

        $id = I('post.goodsId',0,'int');
        if(!$id){
            return errorMsg('确少参数goodsId');
        }

        $_where = array(
            'id' => $id,
        );
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
    public function selectGoods($where=[],$field=[],$join=[]){
        $_where = array(
            'g.status' => 0,
        );
        $_field = array(
            'g.id','g.goods_base_id','g.buy_type','g.sale_price','g.status','g.cash_back',
        );
        $_join = array(
        );
        $list = $this
            ->alias('g')
            ->where(array_merge($_where,$where))
            ->field(array_merge($_field,$field))
            ->join(array_merge($_join,$join))
            ->select();
        return $list?:[];
    }
}