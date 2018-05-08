<?php
namespace app\factory\controller;
class Brand extends FactoryBase
{
    //商标首页
    public function manage()
    {
<<<<<<< HEAD
       $model = new \app\factory\model\Brand;
       $brandList =  $model ->selectBrand();
       $this -> assign('brandList',$brandList);
=======
        $model = new \app\factory\model\Brand;
        $where = [['factory_id','=',$this->factory['factory_id']]];
        $brandList =  $model ->selectBrand($where);
        $this -> assign('brandList',$brandList);
>>>>>>> 01a5a8a43d1e1a3906fc812dea11ef2d9af4cc04
        return $this->fetch();
    }

    //备案
    public function record()
    {
        $model = new \app\factory\model\Brand;
        if(request()->isPost()){
            return $model -> edit($this->factory['factory_id']);
        }
        $categoryModel = new \app\index_admin\model\Category;
        $categoryList = $categoryModel -> selectFirstCategory();
        $this->assign('categoryList',$categoryList);
        if(input('?brand_id')){
            $brandId = input('brand_id');
            $where = array(
                'id' => $brandId,
            );
            $brandInfo =  $model -> getBrand($where);
            $this -> assign('brandInfo',$brandInfo);
        }
        return $this->fetch();
    }



}