<?php
namespace Common\Model;

use Think\Model;
use Think\Model\RelationModel;

class GoodsModel extends Model {
    protected $tableName = 'goods';
    protected $tablePrefix = '';
    protected $connection = 'DB_MYMS';

    protected $_validate = array(
        array('category_id_1','require','所属分类必须！'),
        array('name','require','商品名称必须！'),
        array('price','require','商品原价必须！'),
//        array('name','','分类名称已经存在！',self::EXISTS_VALIDATE,'unique',self::MODEL_BOTH),
    );

    //新增分类
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

    //修改分类
    public function saveGoods(){
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

        $where = array(
            'id' => $id,
        );
       
        $res = $this->where($where)->save();
        
        if($res === false){
            return errorMsg($this->getError());
        }

        $returnArray = array(
            'id' => $id,
        );

        return successMsg('修改成功',$returnArray);
    }

    //删除分类
    public function delGoods(){
        if(!IS_POST){
            return errorMsg(C('NOT_POST'));
        }
        unset($_POST['id']);

        $id = I('post.goodsId',0,'int');
        if(!$id){
            return errorMsg('确少参数goodsId');
        }

        $where = array(
            'id' => $id,
        );

        $res = $this->where($where)->setField('status',2);
        if($res === false){
            return errorMsg($this->getError());
        }

        $returnArray = array(
            'id' => $id,
        );

        return successMsg('删除成功',$returnArray);
    }

    //查询分类
    public function selectGoods($where=[],$field=[],$join=[]){
        $_where = array(
            'g.status' => 0,
        );
        $_field = array(
            'g.id','g.no','g.name','g.status','g.category_id_1','g.category_id_2','g.category_id_3','g.param','g.on_off_line',
            'g.sort','g.specification','g.price','g.group_price','g.discount_price','g.special_price','g.inventory','g.main_img','g.detail_img','g.create_time',
            'g.intro','g.notices','g.usage','g.tag','g.buy_type','g.groupbuy_num'
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
    //查找某件商品信息
    public function getGoodsInfoByGoodsId($goodsId){
        $where['id'] = $goodsId;
        $goodsInfo = D('goods') -> where($where) -> find();

        if($goodsInfo['buy_type']=='1'){
            $goodsInfo['real_price'] = $goodsInfo['discount_price'];
        }
        if($goodsInfo['buy_type']=='2'){
            $goodsInfo['real_price'] = $goodsInfo['special_price'];
        }
        if($goodsInfo['buy_type']=='3'){
            $goodsInfo['real_price'] = $goodsInfo['group_price'];
        }
        return $goodsInfo;
    }
}