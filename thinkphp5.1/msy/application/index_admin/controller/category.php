<?php
namespace app\index_admin\controller;

class Category extends \common\controller\UserBase
{
    //首页
    public function index()
    {
        return $this->fetch();
    }
    //欢迎页
    public function getSecondCategoryById()
    {
        if(request()->isGet()){
            $cat_id_1=(int)input('get.cat_id_1');
            $model = new \app\index_admin\model\Category;
            $secondCategory =  $model -> getSecondCategoryById($cat_id_1);
            $this -> assign('secondCategory',$secondCategory);
            return $this->fetch('template/category_second.html');
        }
    }
}