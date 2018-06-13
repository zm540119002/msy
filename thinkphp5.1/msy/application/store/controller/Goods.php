<?php
namespace app\store\controller;

class Goods extends ShopBase
{
    /**
     * @return array|mixed
     *商品编辑
     */
    public function edit()
    {
        $goodsModel = new \app\store\model\Goods;//商品扩展模型
        if(request()->isPost()){
            return $result = $goodsModel -> edit($this->shop['id']);
        }
        $categoryModel = new \app\index_admin\model\GoodsCategory;
        $where = [['parent_id_1','=',0]];
        $platformCategory = $categoryModel->getList($where);
        $this -> assign('platformCategory',$platformCategory);
        if(input('?goods_id') && $this->shop['id']){
            $goodsId= (int)input('goods_id');
            $where = [
               ['g.shop_id','=',$this->shop['id']],
               ['g.id','=',$goodsId],
            ];
            $goodsInfo =  $goodsModel -> getInfo($where);
            if(empty($goodsInfo)){
                $this->error('此产品已下架');
            }
            $catArray= $goodsInfo['category_id_1'].','.$goodsInfo['category_id_2'];
            $goodsInfo['categoryArray'] = $catArray;
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
        $model = new \app\store\model\Goods;
        $goodsId = (int)input('goods_id');
        if(empty($goodsId) && !$goodsId){
            $this -> error('此商品不存在');
        }
        if($this->shop['id']){
            $where = [
                ['g.id','=',$goodsId],
                ['g.shop_id','=',$this->shop['id']],
            ];
            $goodsInfo =  $model -> getInfo($where);
            if(empty($goodsInfo)){
                $this -> error('此商品不存在');
            }
            $goodsInfo['main_img'] = explode(",",$goodsInfo['main_img']);
            array_pop( $goodsInfo['main_img']);
            $goodsInfo['details_img'] = explode(",",$goodsInfo['details_img']);
            array_pop( $goodsInfo['details_img']);
            $this -> assign('goodsInfo',$goodsInfo);

            //获取店铺的详情信息
            $modelShop = new \app\store\model\Shop;
            $shopInfo = $modelShop -> getShopInfo($this->store);
            $this -> assign('storeInfo',$shopInfo);
        }

        return $this->fetch();
    }

    //生成商品二维码
    public function generateGoodsQRcode(){
        if(request()->isPost()){
            $url = request()->domain().'/index.php/store/Goods/preview/goods_id/'.input('post.goods_id');
            $avatarPath = request()->domain().'/static/common/img/ucenter_logo.png';
            $newRelativePath = config('upload_dir.upload_path').'/';
            $shareQRCodes = createLogoQRcode($url,$avatarPath,$newRelativePath);

            $init = [
                'filename'=>'goods',   //保存目录  ./uploads/compose/goods....
                'title'=>input('post.store_name'),
                'type'=>input('post.store_run_type'),
                'slogan'=>"采购平台·省了即赚到！",
                'name'=>input('post.goods_name'),
                'introduce'=>input('post.specification'),
                'money'=>'￥'.input('post.sale_price').'元',
                'logo'=>$avatarPath, // 60*55px
                'brand'=>input('post.store_logo'), // 160*55
                'goods'=>input('post.goods_thumb_img'), // 460*534
                'qrcode'=>config('upload_dir.upload_path').'/'.$shareQRCodes, // 120*120
                'font'=>'./static/font/simhei.ttf',   //字体
            ];
            return $this->compose($init);
        }

    }


    /**
     * 查出产商相关产品 分页查询
     */
    public function getList(){
        if(!request()->isGet()){
            return errorMsg('请求方式错误');
        }
        $model = new\app\store\model\Goods;
        $where = [
            ['g.shop_id','=',$this->shop['id']],
        ];
        if($_GET['pageType'] == 'promotion' ){//促销
            $where[] =  ['g.sale_type','=',0];
        }
        $file = [
            'g.id,g.sale_price,g.sale_type,g.shelf_status,g.create_time,g.update_time,g.inventory,
                g.name,g.retail_price,g.trait,g.category_id_1,g.category_id_2,g.category_id_3,
                g.thumb_img,g.goods_video,g.main_img,g.details_img,g.tag,g.parameters'
        ];
        $list = $model -> pageQuery($where,$file);
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
            if($_GET['pageType'] == 'sort' ){//入库
                return $this->fetch('list_sort');
            }
        }
    }

    //商品管理展示页
    public function manage(){
        if($this->store && $this->shop){
            //查看本店商品是否存在备份文件
            //存储路径
            $shopPath = realpath(config('upload_dir.upload_path')).'/'.config('upload_dir.store_goods_backup');
            //本厂商店铺备份文件
            $modelShop = new \app\store\model\Shop;
            $shopList = $modelShop -> getShopList($this -> store['id']);
            foreach ( $shopList as &$shopInfo) {
                $fileName = $shopPath.$shopInfo['id'].'.txt';
                if(file_exists($fileName)){
                    //本店铺商品备份文件名
                    if($shopInfo['id'] == $this -> shop['id']){
                        $backupTime = date("Y年m月d日 H:i:s",filemtime($fileName));
                        $selfStore = $shopInfo;
                        $selfshop['backup_time'] = $backupTime;
                        $this -> assign('selfStore',$selfStore);
                    }else{
                        //本厂商其他店铺商品备份文件
                        $otherShop[] = $shopInfo;
                        $this -> assign('otherStores',$otherShop);
                    }
                }
            }

        }
        return $this->fetch();
    }
    //设置商品排序
    public function setSort(){
        if($this->store && $this->store) {
            if (request()->isPost()) {
                $data = input();
                $model = new \app\store\model\Goods;
                $result = $model->allowField(true)
                    ->save($data, ['id' => $data['goodsId'], 'store_type' => $data['storeType']]);
                if (false !== $result) {
                    return successMsg('成功');
                }
            }
        }
        return $this -> fetch();
    }
    //上下架设置
    public function setShelf(){
        if($this->store && $this->store) {
            if (request()->isPost()) {
                $data = input();
                if(empty($data['goodsId']) && !(int)($data['goodsId'])){
                    return errorMsg('参数错误');
                }
                $model = new \app\store\model\Goods;
                $result = $model->allowField(true)
                    ->save($data, ['id' => $data['goodsId'], 'shop_id' => $this->shop['id']]);
                if (false !== $result) {
                    return successMsg('成功');
                }
            }
        }
        return $this -> fetch();
    }

    //设置商品库存
    public function setInventory(){
        if(request()->isPost()){
            $model = new \app\store\model\Goods;
            return $result = $model ->setInventory($this->shop['id']);
        }
        return $this -> fetch();
    }

    //商品备份
    public function backup(){
        if(request()->isPost()){
            $model = new \app\store\model\Goods;
            $where = [
                ['shop_id','=',$this->shop['id']]
            ];
            $field = [
                'g.id,g.name,g.trait,thumb_img,g.main_img,g.goods_video,g.parameters,g.details_img,
            g.retail_price,g.retail_price,g.sale_price,g.settle_price'
            ];
            $goodsList = $model -> getList($where,$field);
            $goodsList = json_encode($goodsList);
            $uploadPath = config('upload_dir.upload_path');
            if(!is_dir($uploadPath)){
                if(!mk_dir($uploadPath)){
                    return  errorMsg('创建Uploads目录失败');
                }
            }
            $uploadPath = realpath($uploadPath);
            //存储路径
            $shopPath = $uploadPath.'/'.config('upload_dir.store_goods_backup');
            if(!mk_dir($shopPath)){
                return errorMsg('创建临时目录失败');
            }
            //按店Id为文件名保存数据
            $fileName = $shopPath.$this->shop['id'].'.txt';
            file_put_contents($fileName,$goodsList);
            //创建时间
            $fileCreateTime = ['create_time'=>date("Y年m月d日 H:i:s",filemtime($fileName))];
            return successMsg('成功',$fileCreateTime);
        }
    }

    //获取备份
    public function getBackup(){
        if(!request()->isGet()){
            return errorMsg('请求方式错误');
        }
        $shopId = (int)input('get.storeId');
        $modelStore = new \app\store\model\Store;
        if(!$modelStore -> checkStoreExist($shopId,$this -> store['id'])){
            return errorMsg('不存在店铺');
        }
        //存储路径
        $shopPath = realpath(config('upload_dir.upload_path')).'/'.config('upload_dir.store_goods_backup');
        $shopBackupFile = $shopPath.$this->shop['id'].'.txt';
        $backup = file_get_contents($shopBackupFile);
        $goodsListBackup = json_decode($backup,true);
        $pageSize = (isset($_GET['pageSize']) && intval($_GET['pageSize'])) ?
            input('get.pageSize',0,'int') : config('custom.default_page_size');
        $start = $pageSize* (input('get.page',0,'int')-1);
        $end = $pageSize* input('get.page',0,'int')-1;
        $goodsList = [];
        foreach ($goodsListBackup as $k=>$v){
            if($k>=$start && $k<= $end){
                $goodsList[] = $v;
            }elseif($k>$end){
                break;
            }
        }
        $this -> assign('goodsListBackup',$goodsList);
        return $this -> fetch('list_backup');
    }
    
}