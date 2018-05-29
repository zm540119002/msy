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
        $goodsModel = new \app\factory\model\Goods;//商品扩展模型
        if(request()->isPost()){
            return $result = $goodsModel -> edit($this->store['id']);
        }
        $categoryModel = new \app\index_admin\model\Category;
        $platformCategory = $categoryModel->selectFirstCategory();
        $this -> assign('platformCategory',$platformCategory);
        if(input('?goods_id')){
            $goodsId= (int)input('goods_id');
            $where = [
               ['g.store_id','=',$this->store['id']],
               ['g.id','=',$goodsId],
            ];
            $goodsInfo =  $goodsModel -> getInfo($where);
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
        $goodsInfo =  $model -> getInfo($where,$file,$join);
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
        if(!request()->isGet()){
            return errorMsg('请求方式错误');
        }
        $model = new\app\factory\model\Goods;
        $where = [
            ['g.store_id','=',$this->store['id']],
        ];
        if($_GET['pageType'] == 'promotion' ){//促销
            $where[] =  ['g.sale_type','=',0];
        }
        $file = [
            'g.id,g.sale_price,g.sale_type,g.shelf_status,g.create_time,g.update_time,g.inventory,
                g.name,g.retail_price,g.trait,g.cat_id_1,g.cat_id_2,g.cat_id_3,
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
        $this->assign('factory',$this->factory);
        //查看本店商品是否存在备份文件
        //存储路径
        $storePath = realpath(config('upload_dir.upload_path')).'/'.config('upload_dir.factory_goods_backup');
        //本厂商店铺备份文件
        $modelStore = new \app\factory\model\Store;
        $storeList = $modelStore -> getStoreList($this -> factory['factory_id']);
        foreach ( $storeList as &$storeInfo) {
            $fileName = $storePath.$storeInfo['id'].'.txt';
            if(file_exists($fileName)){
                //本店铺商品备份文件名
                if($storeInfo['id'] == $this -> store['id']){
                    $backupTime = date("Y年m月d日 H:i:s",filemtime($fileName));
                    $selfStore = $storeInfo;
                    $selfStore['backup_time'] = $backupTime;
                    $this -> assign('selfStore',$selfStore);
                }else{
                    //本厂商其他店铺商品备份文件
                    $otherStores[] = $storeInfo;
                    $this -> assign('otherStores',$otherStores);
                }
            }
        }
        return $this->fetch();
    }
    //设置商品库存
    public function setSort(){
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
    //上下架设置
    public function setShelf(){
        if(request()->isPost()){
            $data = input();
            $model = new \app\factory\model\Goods;
            $result = $model->allowField(true)
                ->save($data, ['id' => $data['goodsId'],'store_id'=>$this->store['id']]);
           if(false !== $result){
               return successMsg('成功');
           }
        }
        return $this -> fetch();
    }

    //设置商品库存
    public function setInventory(){
        if(request()->isPost()){
            $model = new \app\factory\model\Goods;
            return $result = $model ->setInventory($this->store['id']);
        }
        return $this -> fetch();
    }

    //商品备份
    public function backup(){
        if(request()->isPost()){
            $model = new \app\factory\model\Goods;
            $where = [
                ['store_id','=',$this->store['id']]
            ];
            $field = [
                'g.name,g.trait,thumb_img,g.main_img,g.goods_video,g.parameters,g.details_img,
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
            $storePath = $uploadPath.'/'.config('upload_dir.factory_goods_backup');
            if(!mk_dir($storePath)){
                return errorMsg('创建临时目录失败');
            }
            //按店Id为文件名保存数据
            $fileName = $storePath.$this->store['id'].'.txt';
            file_put_contents($fileName,$goodsList);
            //创建时间
            $fileCreateTime = ['create_time'=>date("Y年m月d日 H:i:s",filemtime($fileName))];
            return successMsg('成功',$fileCreateTime);
        }
    }

    public function getBackup(){
        if(!request()->isGet()){
            return errorMsg('请求方式错误');
        }
        $storeId = (int)input('get.storeId');
        $modelStore = new \app\factory\model\Store;
        if(!$modelStore -> checkStoreExist($storeId,$this -> factory['factory_id'])){
            return errorMsg('不存在店铺');
        }
        //存储路径
        $storePath = realpath(config('upload_dir.upload_path')).'/'.config('upload_dir.factory_goods_backup');
        $storeBackupFile = $storePath.$this->store['id'].'.txt';
        $backup = file_get_contents($storeBackupFile);
        $goodsListBackup = json_decode($backup,true);
        $this -> assign('goodsListBackup',$goodsListBackup);
        return $this -> fetch('List_backup');
        print_r($backup);exit;


    }
}