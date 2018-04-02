<?php
namespace app\factory\controller;
use app\factory\model\Brand as M;
use app\index_admin\model\Category as categoryModel;
use common\controller\Base;
class Brand extends Base
{
    //商标首页
    public function index()
    {
        $model = new M();
        return $model ->selectBrand([],[],['id'=>'desc'],[],'2,3');
        return $this->fetch();
    }

    //备案
    public function record()
    {
        $model = new M();
        if(request()->isPost()){
            return input();
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
            $field = array(
                'id'
            );
            $brandInfo =  $model -> getBrand($where,$field);
            $this -> assign('brandInfo',$brandInfo);
        }
        return $this->fetch();
    }



}