<?php
namespace Common\Model;

use Think\Model;
use Think\Model\RelationModel;

class GoodsModel extends Model {
    protected $tableName = 'goods';
    protected $tablePrefix = '';
    protected $connection = 'DB_CONFIG1';

    protected $_validate = array(
        array('category_id_1','require','所属必须！'),
        array('name','require','商品名称必须！'),
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
            'g.id','g.no','g.name','g.status','g.category_id_1','g.category_id_2','g.category_id_3','g.on_off_line',
            'g.sort','g.specification','g.price','g.special_price','g.vip_price','g.senior_vip_price','g.gold_vip_price',
            'g.inventory','g.main_img','g.detail_img','g.create_time','g.intro','g.notices','g.tag',
            'g.single_specification','g.package_num','g.package_unit','g.purchase_unit',
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