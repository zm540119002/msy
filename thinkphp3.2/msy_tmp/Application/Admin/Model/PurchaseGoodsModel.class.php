<?php
namespace Admin\Model;

use Think\Model;

class PurchaseGoodsModel extends Model {
    protected $tableName = 'goods';
    protected $tablePrefix = '';
    protected $connection = 'DB_CONFIG2';

    protected $_validate = array(
        array('goods_category_id','require','请选择分类！'),
        array('name','require','请填写商品名称！'),
        array('price','require','请填写商品价格！'),
    );

    //新增分类
    public function addGoods($data){
        if(!IS_POST){
            return errorMsg(C('NOT_POST'));
        }
        unset($_POST['id']);

//        $res = $this->create();
//        if(!$res){
//            return errorMsg($this->getError());
//        }

        $id = $this->add($data);
        if($id === false){
            return errorMsg($this->getError());
        }

        $returnArray = array(
            'id' => $id,
        );

        return successMsg('新增成功',$returnArray);
    }

    //修改分类
    public function saveGoods($data){
        if(!IS_POST){
            return errorMsg(C('NOT_POST'));
        }
        unset($_POST['id']);

        $id = I('post.goodsId',0,'int');
        if(!$id){
            return errorMsg('确少参数goodsId');
        }

//        $res = $this->create();
//        if(!$res){
//            return errorMsg($this->getError());
//        }

        $where = array(
            'id' => $id,
        );
        $res = $this->where($where)->save($data);
        if($res === false){
            $this->error($this->getError());
        }

        $returnArray = array(
            'id' => $id,
        );

        return successMsg('修改成功',$returnArray);
    }

    //删除分类
    public function delGoods($ids=[]){
        $where = array(
            'id'=>array('in',$ids),
        );
        $res = $this->where($where)->setField('status',2);
        if($res === false){
            return errorMsg('删除失败');
        }
        $returnArray = array(
            'id' => $ids,
        );
        return successMsg('删除成功',$returnArray);
    }

    //查询分类
    public function selectGoods($where=[],$field=[],$join=[]){
        $_where = array(
            'g.state' => 1,
        );
        $_field = array(
            'g.id','g.name','g.state','g.goods_category_id','g.goods_category_id_1','g.goods_category_id_2','g.goods_category_id_3','g.sort',
            'g.price','g.norms','g.storage','g.tag','g.intro','g.parameter','g.goods_content','g.detail_imgs','g.first_img'
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