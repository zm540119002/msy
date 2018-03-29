<?php
namespace app\factory\controller;
use app\factory\model\Goods as M;
use common\controller\Base;
use app\index_admin\model\Category as CategoryModel;
class Goods extends Base
{
    public function index()
    {
        return $this->fetch('template/category_second_template.html');
    }

    public function add()
    {
        if(request()->isPost()){
            $model = new M();
            return $model -> add();
        }
        $categoryModel = new CategoryModel();
        $platformCategory = $categoryModel->select();
        $this->assign('platformCategory',$platformCategory);
        return $this->fetch();
    }



}