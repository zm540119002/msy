<?php
namespace app\factory\controller;
use app\factory\model\Brand as M;
use app\index_admin\model\Category as categoryModel;
class Brand extends FactoryBase
{
    //商标首页
    public function index()
    {
        $model = new M();
        $brandList =  $model ->selectBrand();
        $this -> assign('brandList',$brandList);
        return $this->fetch();
    }

    //备案
    public function record()
    {
        $model = new M();
        if(request()->isPost()){
            if(input('?post.brand_id')){
                return $model -> edit();
            }else{
                return $model -> add();
            }
        }
        $categoryModel = new categoryModel;
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