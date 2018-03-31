<?php
namespace app\factory\controller;
use app\factory\model\Goods as M;
use common\controller\Base;
use app\index_admin\model\Category as CategoryModel;
class Goods extends Base
{
    public function index()
    {
        return $this->fetch();
    }

    /**
     * @return array|mixed
     *商品编辑
     */
    public function edit()
    {
        if(request()->isPost()){
            $model = new M();
            return $model -> add();
        }
        $categoryModel = new CategoryModel();
        $platformCategory = $categoryModel->selectFirstCategory();
        $this -> assign('platformCategory',$platformCategory);
        if(request()->isGet()){
            $model = new M();
            return $model -> add();
        }
        return $this->fetch();
    }



}