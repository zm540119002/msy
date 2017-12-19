<?php
namespace Common\Model;

use Think\Model;
use Think\Model\RelationModel;

class GoodsBaseModel extends Model {
    protected $tableName = 'goods_base';
    protected $tablePrefix = '';
    protected $connection = 'DB_CONFIG1';

    protected $_validate = array(
        array('category_id_1','require','所属必须！'),
        array('name','require','商品名称必须！'),
        array('price','require','商品原价必须！'),
    );

    //新增
    public function addGoodsBase(){
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
    public function saveGoodsBase($where=array()){
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
    public function delGoodsBase($where=array()){
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
    public function selectGoodsBase($where=[],$field=[],$join=[]){
        $_where = array(
            'gb.status' => 0,
        );
        $_field = array(
            'gb.id','gb.no','gb.name','gb.status','gb.category_id_1','gb.category_id_2','gb.category_id_3','gb.on_off_line','gb.param','gb.usage',
            'gb.sort','gb.specification','gb.price', 'gb.inventory','gb.main_img','gb.detail_img','gb.create_time','gb.intro','gb.notices','gb.tag'
        ,'gb.package_num','gb.package_unit','gb.purchase_unit','gb.single_specification'

        );
        $_join = array(
        );
        $list = $this
            ->alias('gb')
            ->where(array_merge($_where,$where))
            ->field(array_merge($_field,$field))
            ->join(array_merge($_join,$join))
            ->select();
        return $list?:[];
    }
}