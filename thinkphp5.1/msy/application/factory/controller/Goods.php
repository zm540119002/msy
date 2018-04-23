<?php
namespace app\factory\controller;
use app\factory\model\Goods as M;
use common\controller\Base;
use app\index_admin\model\Category as categoryModel;
use app\factory\model\Series as seriesModel;
use GuzzleHttp\Psr7\Request;

class Goods extends FactoryBase
{
    public function index()
    {
        $seriesModel = new seriesModel();
        $where = [
            ['factory_id','=',$this->factory['id']],
        ];
        $seriesList = $seriesModel -> selectSeries($where,[],['sort'=>'desc','id'=>'desc',]);
        $this -> assign('seriesList',$seriesList);
        $model = new M();
        $goodsList = $model -> selectGoods();
        $this -> assign('goodsList',$goodsList);
        return $this->fetch();
    }

    /**
     * @return array|mixed
     *商品编辑
     */
    public function edit()
    {
        $model = new M();
        if(request()->isPost()){
            if(input('?post.goods_id') && !input('?post.goods_id') == ''){
                return $model -> edit($this->factory['id']);
            }
            return $model -> add($this->factory['id']);
        }
        $categoryModel = new categoryModel();
        $platformCategory = $categoryModel->selectFirstCategory();
        $this -> assign('platformCategory',$platformCategory);
        $seriesModel = new seriesModel();
        $where = [
            ['factory_id','=',$this->factory['id']],
        ];
        $seriesList = $seriesModel -> selectSeries($where,[],['sort'=>'desc','id'=>'desc',]);
        $this -> assign('seriesList',$seriesList);
        if(input('?goods_id')){
            $goodsId = (int)input('goods_id');
            $where = [
               ['id','=',$goodsId],
               ['factory_id','=',$this->factory['id']],
            ];
            $goodsInfo =  $model -> getGoods($where);
            if(empty($goodsInfo)){
                $this->error('此产品已下架');
            }
            $catArray= $goodsInfo['cat_id_1'].','.$goodsInfo['cat_id_2'];
            $goodsInfo['catArray'] = $catArray;
            $this -> assign('goodsInfo',$goodsInfo);
        }
        return $this->fetch();
    }


    /**
     * @return array|mixed
     *商品预览
     */
    public function preview()
    {
        $model = new M();
        if(input('?goods_id')){
            $goodsId = (int)input('goods_id');
            $where = [
                ['id','=',$goodsId],
            ];
            $goodsInfo =  $model -> getGoods($where);
            $this -> assign('goodsInfo',$goodsInfo);
        }
        return $this->fetch();
    }


    /**
     * 查出产商相关产品
     */
    public function getList(){
        $model = new M();
        $list = $model -> pageQuery();
        $this->assign('list',$list);
        return $this->fetch('goods_list');
    }


}