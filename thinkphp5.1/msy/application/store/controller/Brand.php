<?php
namespace app\store\controller;
class Brand extends StoreBase
{
    //商标首页
    public function manage()
    {
        $model = new \app\store\model\Brand;
        $where = [
            ['store_id','=',$this->store['id']]
        ];
        $brandList =  $model -> getList($where);
        $this -> assign('brandList',$brandList);
        return $this->fetch();
    }

    //备案
    public function record()
    {
        $model = new \app\store\model\Brand;
        if(request()->isPost()){
            return $model -> edit($this->store['id']);
        }
        $categoryModel = new \app\index_admin\model\GoodsCategory;
        $where = [
            ['parent_id_1','=',0]
        ];
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