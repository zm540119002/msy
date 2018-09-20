<?php
namespace app\factory\controller;
class Brand extends FactoryBase
{
    //商标首页
    public function manage()
    {
        $model = new \common\model\Brand;
        $config = [
            'where' => [
                ['factory_id','=',$this->factory['id']]
            ],
        ];
        $brandList =  $model -> getList($config);
        $this -> assign('brandList',$brandList);
        return $this->fetch();
    }

    //备案
    public function record()
    {
        $model = new \common\model\Brand;
        if(request()->isPost()){
            return $model -> edit($this->factory['id']);
        }
        $categoryModel = new \app\index_admin\model\GoodsCategory;
        $where = [
            ['parent_id_1','=',0]
        ];
        $categoryList = $categoryModel->getList($where);
        $this->assign('categoryList',$categoryList);
        if(input('?brand_id')){
            $brandId = input('brand_id');
            $config = [
                'where' => [
                    ['id', '=', $brandId],
                ],
            ];
            $brandInfo =  $model -> getInfo($config);
            $this -> assign('brandInfo',$brandInfo);
        }
        return $this->fetch();
    }
}