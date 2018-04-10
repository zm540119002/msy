<?php
namespace app\index_admin\controller;

use think\Controller;
use think\Request ;
use think\Db;
use app\index_admin\model\Category as M;

class Category extends \common\controller\UserBase
{
    //获取一级菜单
    public function getFirstCategory()
    {
        $model = new M();
        return $model ->selectFirstCategory();
    }

    //获取二级菜单
    public function getSecondCategoryById()
    {
        if(request()->isGet()){
            $cat_id_1 = (int)input('get.cat_id_1');
            $model = new M();
            $secondCategory = $model ->getSecondCategoryById($cat_id_1);
            $this -> assign('secondCategory',$secondCategory);
            return $this -> fetch('template/category_second_template.html');
        }

    }
    
}
