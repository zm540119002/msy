<?php
namespace app\factory\controller;

use common\controller\Base;

class Record extends Base
{
    /**首页
     */
    public function index()
    {
        return $this->fetch();
    }
    /**首页
     */
    public function edit()
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

    /**预览
     */
    public function preview()
    {
        return $this->fetch();
    }


}