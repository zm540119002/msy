<?php
namespace app\factory\controller;

class Goods extends StoreBase
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
            $result = $goodsBaseModel -> edit($this->store['id']);
            if(!$result['status']){
                $goodsBaseModel ->rollback();
                return errorMsg('失败');
            }
            //编辑商品表
            $goodsBaseId =  $result['id'];
            $result = $goodsModel -> edit($goodsBaseId,$this->store['id']);
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
//        $seriesModel = new \app\factory\model\Series;
//        $where = [
//            ['store_id','=',$this->store['id']],
//        ];
//        $seriesList = $seriesModel -> selectSeries($where,[],['sort'=>'desc','id'=>'desc',]);
//        $this -> assign('seriesList',$seriesList);
        if(input('?goods_base_id')){
            $goodsBaseId = (int)input('goods_base_id');
            $where = [
               ['gb.store_id','=',$this->store['id']],
               ['g.goods_base_id','=',$goodsBaseId],
            ];
            $file = [
                'g.goods_base_id,g.id,g.sale_price,g.sale_type,g.shelf_status,g.create_time,g.update_time,
                gb.name,gb.retail_price,gb.trait,gb.cat_id_1,gb.cat_id_2,gb.cat_id_3,
                gb.thumb_img,gb.goods_video,gb.main_img,gb.details_img,gb.tag,gb.parameters'
            ];
            $join =[
                ['goods g','gb.id = g.goods_base_id'],
            ];
            $goodsInfo =  $goodsBaseModel -> getGoodsBase($where,$file,$join);
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
        $model = new \app\factory\model\Goods;
        $goodsId = (int)input('goods_id');
        if(empty($goodsId) && !$goodsId){
            $this -> error('此商品不存在');
        }
        $where = [
            ['g.id','=',$goodsId],
            ['g.store_id','=',$this->store['id']],
        ];
        $file = ['g.id,g.goods_base_id,g.sale_price,g.sale_type,g.create_time,g.update_time,
                    gb.name,gb.retail_price,gb.trait,gb.cat_id_1,gb.cat_id_2,gb.cat_id_3,gb.thumb_img,
                    gb.main_img,gb.goods_video,gb.parameters,gb.details_img'];
        $join = [  ['goods_base gb','gb.id = g.goods_base_id'],];
        $goodsInfo =  $model -> getGoods($where,$file,$join);
        $goodsInfo['main_img'] = explode(",",$goodsInfo['main_img']);
        array_pop( $goodsInfo['main_img']);
        $goodsInfo['details_img'] = explode(",",$goodsInfo['details_img']);
        array_pop( $goodsInfo['details_img']);
        $this -> assign('goodsInfo',$goodsInfo);

        //获取店铺的详情信息
        $modelStore = new \app\factory\model\Store;
        $storeInfo = $modelStore -> getStoreInfo($this->store);
        $this -> assign('storeInfo',$storeInfo);
        return $this->fetch();
    }

    //生成商品二维码
    public function generateGoodsQRcode(){
        if(request()->isPost()){
            $url = request()->domain().'/index.php/factory/Goods/preview/goods_id/'.input('post.goods_id');
            $avatarPath = request()->domain().'/static/common/img/ucenter_logo.png';
            $newRelativePath = config('upload_dir.upload_path').'/';
            $shareQRCodes = createLogoQRcode($url,$avatarPath,$newRelativePath);

            $init = [
                'filename'=>'goods',   //保存目录  ./uploads/compose/goods....
                'title'=>'美尚官方旗舰店',
                'type'=>'供应商自营',
                'slogan'=>"采购平台·省了即赚到！",
                'name'=>input('post.name'),
                'introduce'=>input('post.specification'),
                'money'=>'￥'.input('post.sale_price').'元',
                'logo'=>$avatarPath, // 60*55px
                'brand'=>input('post.store_logo'), // 160*55
                'goods'=>input('post.goods_thumb_img'), // 460*534
                'qrcode'=>config('upload_dir.upload_path').'/'.$shareQRCodes, // 120*120
                'font'=>'./static/font/simhei.ttf',   //字体
            ];
            return successMsg($this->compose($init));
        }

    }


    /**
     * 查出产商相关产品 分页查询
     */
    public function getList(){
        $model = new\app\factory\model\Goods;
        $where = [
            ['gb.store_id','=',$this->store['id']],
        ];
        $list = $model -> pageQuery($where);
        $this->assign('list',$list);
        if(isset($_GET['pageType'])){
            if($_GET['pageType'] == 'promotion' ){//促销
                return $this->fetch('list_promotion');
            }
            if($_GET['pageType'] == 'shelf' ){//上架管理
                return $this->fetch('list_shelf');
            }
            if($_GET['pageType'] == 'manage' ){//管理
                return $this->fetch('list_manage');
            }
            if($_GET['pageType'] == 'inventory' ){//入库
                return $this->fetch('list_inventory');
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
            $result = $model->allowField(true)
                ->save($data, ['id' => $data['goodsId'],'store_type'=>$data['storeType']]);
           if(false !== $result){
               return successMsg('成功');
           }
        }
    }

    //设置商品库存
    public function setInventory(){
        if(request()->isPost()){
            $data = input();
            $model = new \app\factory\model\Goods;
            $result = $model->allowField(true)
                ->save($data, ['id' => $data['goodsId'],'store_type'=>$data['storeType']]);
            if(false !== $result){
                return successMsg('成功');
            }
        }
        return $this -> fetch();
    }
}