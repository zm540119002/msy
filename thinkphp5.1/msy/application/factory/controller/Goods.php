<?php
namespace app\factory\controller;

class Goods extends FactoryBase
{
    /**
     * @return array|mixed
     *商品编辑
     */
    public function edit()
    {
        $goodsBaseModel = new \app\factory\model\GoodsBase;//商品基础表
        $goodsModel = new \app\factory\model\Goods;//商品扩展模型
        if(request()->isPost()){
            //编辑商品基础表
            $goodsBaseModel -> startTrans();
            $result = $goodsBaseModel -> edit($this->factory['factory_id']);
            if(!$result['status']){
                $goodsBaseModel ->rollback();
                return errorMsg('失败');
            }
            //编辑商品表
            $goodsBaseId =  $result['id'];
            $result = $goodsModel -> edit($goodsBaseId,$this->factory['factory_id']);
            if(!$result['status']){
                $goodsModel ->rollback();
                return errorMsg('失败');
            }
            $goodsBaseModel ->commit();
            return successMsg('成功');

        }
        $categoryModel = new \app\index_admin\model\Category;
        $platformCategory = $categoryModel->selectFirstCategory();
        $this -> assign('platformCategory',$platformCategory);
        $seriesModel = new \app\factory\model\Series;
        $where = [
            ['factory_id','=',$this->factory['factory_id']],
        ];
        $seriesList = $seriesModel -> selectSeries($where,[],['sort'=>'desc','id'=>'desc',]);
        $this -> assign('seriesList',$seriesList);
        if(input('?goods_base_id')){
            $goodsBaseId = (int)input('goods_base_id');
            $where = [
               ['gb.factory_id','=',$this->factory['factory_id']],
               ['g.goods_base_id','=',$goodsBaseId],
            ];
            $file = [
                'g.goods_base_id,g.id,g.sale_price,g.sale_type,g.shelf_status,g.create_time,g.update_time,g.store_type,
                gb.name,gb.retail_price,gb.trait,gb.cat_id_1,gb.cat_id_2,gb.cat_id_3,
                gb.thumb_img,gb.goods_video,gb.main_img,gb.details_img,gb.tag,gb.create_time,gb.update_time,
                gb.parameters'
            ];
            $join =[
                ['goods_base gb','gb.id = g.goods_base_id'],
            ];
            $goodsInfo =  $goodsModel -> selectGoods($where,$file,$join);
            if(empty($goodsInfo)){
                $this->error('此产品已下架');
            }
            $catArray= $goodsInfo[0]['cat_id_1'].','.$goodsInfo[0]['cat_id_2'];
            $goodsInfo[0]['catArray'] = $catArray;
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
        $model = new \app\factory\model\Goods;
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
     * 查出产商相关产品 分页查询
     */
    public function getList(){
        $model = new\app\factory\model\Goods;
        $where = [
            ['factory_id','=',$this->factory['factory_id']],
        ];
        $list = $model -> pageQuery($where);
        $this->assign('list',$list);
        if(isset($_GET['pageType'])){
            if($_GET['pageType'] == 'promotion' ){
                return $this->fetch('list_promotion');
            }
            if($_GET['pageType'] == 'shelf' ){
                return $this->fetch('list_shelve');
            }
            if($_GET['pageType'] == 'manage' ){
                return $this->fetch('list_manage');
            }
        }
    }
    //商品管理展示页
    public function manage(){
        $this->assign('factory',$this->factory);
        return $this->fetch();
    }
    //上下架设置
    public function setShelf(){
        if(request()->isPost()){
            $data = input();
            $model = new \app\factory\model\Goods;
            if(isset($data['storeType'])){
                if($data['storeType'] == 'purchases'){
                    $data['purchases_shelf'] = $data['shelfStatus'];
                }
                if($data['storeType'] == 'commission'){
                    $data['commission_shelf'] = $data['shelfStatus'];
                }
                if($data['storeType'] == 'retail'){
                    $data['retail_shelf'] = $data['shelfStatus'];
                }
            }
            $result = $model->allowField(true)
                ->save($data, ['id' => $data['goodsId'],'factory_id'=>$this->factory['factory_id'],$data['storeType']=>1]);
           if(false !== $result){
               return successMsg('成功');
           }
        }
    }



}