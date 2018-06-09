<?php
namespace app\factory\controller;
class Brand extends FactoryBase
{
    //商标首页
    public function manage()
    {
        $model = new \app\factory\model\Brand;
        $where = [['factory_id','=',$this->factory['id']]];
        $brandList =  $model -> getList($where);
        $this -> assign('brandList',$brandList);
        return $this->fetch();
    }

    //备案
    public function record()
    {
        $model = new \app\factory\model\Brand;
        if(request()->isPost()){
            return $model -> edit($this->factory['id']);
        }
        $categoryModel = new \app\index_admin\model\GoodsCategory;
        $where = [['parent_id_1','=',0]];
        $categoryList = $categoryModel->getList($where);
        $this->assign('categoryList',$categoryList);
        if(input('?brand_id')){
            $brandId = input('brand_id');
            $where = array(
                'id' => $brandId,
            );
            $brandInfo =  $model -> getInfo($where);
            $this -> assign('brandInfo',$brandInfo);
        }
        return $this->fetch();
    }
}