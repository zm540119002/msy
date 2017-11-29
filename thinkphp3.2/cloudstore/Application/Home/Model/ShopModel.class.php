<?php
namespace Home\Model;

use Think\Model;
use Think\Model\RelationModel;

class ShopModel extends Model {
    protected $tableName = 'shop';
    protected $tablePrefix = '';
    protected $connection = 'DB_CLOUD_STORE';

    protected $_validate = array(
//        array('category_id_1','require','所属分类必须！'),
//        array('name','require','商品名称必须！'),
//        array('price','require','商品原价必须！'),
//        array('name','','分类名称已经存在！',self::EXISTS_VALIDATE,'unique',self::MODEL_BOTH),
    );

    //新增分类
    public function addShop(){
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
    public function saveShop(){
        if(!IS_POST){
            return errorMsg(C('NOT_POST'));
        }
        unset($_POST['id']);

        $id = I('post.shopId',0,'int');
        if(!$id){
            return errorMsg('确少参数shopId');
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
    public function delShop(){
        if(!IS_POST){
            return errorMsg(C('NOT_POST'));
        }
        unset($_POST['id']);

        $id = I('post.shopId',0,'int');
        if(!$id){
            return errorMsg('确少参数shopId');
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

    //查询门店
    protected function selectShop($where=[],$field=[],$join=[]){
        $_where = array(
            's.status' => 0,
        );
        $_field = array(
            's.id','s.name','s.type','s.address','s.receptionist_mobile','s.fixed_phone','s.logo',
            's.company_id','c.name company_name','c.type company_type','s.identified',
        );
        $_join = array(
            ' left join company c on s.company_id = c.id ',
        );
        $list = $this
            ->alias('s')
            ->where(array_merge($_where,$where))
            ->field(array_merge($_field,$field))
            ->join(array_merge($_join,$join))
            ->select();
        return $list?:[];
    }
}