<?php
namespace app\store\controller;

use common\controller\UserBase;

class GoodsCategory extends UserBase
{

    /**获取二级分类
     * @return mixed
     *
     */
    public function getSecondCategoryById()
    {
        if(request()->isGet()){
            $category_id_1=(int)input('get.category_id_1');
            $categoryModel = new \common\model\GoodsCategory;
            $config = [
                'where' => [
                    ['parent_id_1','=',$category_id_1]
                ],
            ];
            $secondCategory = $categoryModel->getList($config);
            $this -> assign('secondCategory',$secondCategory);
            return $this->fetch('template/category_second.html');
        }
    }
}