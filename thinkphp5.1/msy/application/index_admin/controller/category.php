<?php
namespace app\index_admin\controller;

class Category extends \common\controller\UserBase
{
    //首页
    public function index()
    {
        return $this->fetch();
    }
    //
    public function getSecondCategoryById()
    {
        if(request()->isGet()){
            $category_id_1=(int)input('get.category_id_1');
            $model = new \app\index_admin\model\Category;
            $secondCategory =  $model -> getSecondCategoryById($category_id_1);
            $this -> assign('secondCategory',$secondCategory);
            return $this->fetch('template/category_second.html');
        }
    }
}