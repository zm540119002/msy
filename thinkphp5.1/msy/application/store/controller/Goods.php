<?php
namespace app\store\controller;

class Goods extends \common\controller\FactoryStoreBase
{
    /**
     * @return array|mixed
     *商品编辑
     */
    public function edit()
    {
        $goodsModel = new \common\model\Goods;//商品扩展模型
        if(request()->isPost()){
            return $result = $goodsModel -> edit($this->store['id'],$this->store['run_type']);
        }
        $categoryModel = new \common\model\GoodsCategory;
        $config = [
            'where' => [
                ['parent_id_1','=',0]
            ],
        ];
        $platformCategory = $categoryModel->getList($config);
        $this -> assign('platformCategory',$platformCategory);
        if(input('?goods_id') &&  $this->store){
            $goodsId= (int)input('goods_id');
            $config=[
                'where'=>[
                    ['g.store_id','=',$this->store['id']],
                    ['g.id','=',$goodsId],
                ],
            ];
            $goodsInfo =  $goodsModel -> getInfo($config);
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
     *商品删除
     */
    public function del()
    {
        if(!request()->isPost()){
            return errorMsg('请求方式错误');
        }
        $model = new \common\model\Goods;
        $goodsId = (int)input('goods_id');
        if(empty($goodsId) && !$goodsId){
            $this -> error('此商品不存在');
        }
        if($this->store){
            $condition=[
                ['id','=',$goodsId],
                ['store_id','=',$this->store['id']],
            ];
            return $model -> del($condition);
        }
    }


    /**
     * @return array|mixed
     *商品预览
     */
    public function preview()
    {
        $model = new \common\model\Goods;
        $goodsId = (int)input('goods_id');
        if(empty($goodsId) && !$goodsId){
            $this -> error('此商品不存在');
        }
        if($this->store){
            $config=[
                'where'=>[
                    ['g.id','=',$goodsId],
                    ['g.store_id','=',$this->store['id']],
                ],
            ];
            $goodsInfo =  $model -> getInfo($config);
            if(empty($goodsInfo)){
                $this -> error('此商品不存在');
            }
            $goodsInfo['main_img'] = explode(",",rtrim($goodsInfo['main_img'], ","));
            $goodsInfo['details_img'] = explode(",",rtrim($goodsInfo['details_img'], ","));
            $this -> assign('goodsInfo',$goodsInfo);
        }
        return $this->fetch();
    }

    //生成商品二维码
    /**
     * @return array
     */
    public function generateGoodsQRcode(){
        if(request()->isPost()){
            $url = request()->domain().'/index.php/factory/Goods/preview/goods_id/'.input('post.goods_id');
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
        $model = new\common\model\Goods;
        $config=[
            'where'=>[
                ['g.store_id','=',$this->store['id']],
                ['g.status','=',0],
            ],
            'field'=>[
                    'g.id,g.sale_price,g.sale_type,g.shelf_status,g.create_time,g.update_time,g.inventory,
                g.name,g.retail_price,g.trait,g.category_id_1,g.category_id_2,g.category_id_3,
                g.thumb_img,g.goods_video,g.main_img,g.details_img,g.tag,g.parameters,g.sort'
            ],
            'order'=>[
                'sort'=>'desc',
                'line_num'=>'asc',
                'id'=>'desc'
            ],
        ];
        if($_GET['pageType'] == 'promotion' ) {//促销
            $config['where'][] = ['g.sale_type', '=', 0];
        }
        $keyword = input('get.keyword','');
        if($keyword){
            $config['where'][] = ['name', 'like', '%'.trim($keyword).'%'];
        }

        $list = $model -> pageQuery($config);
        $page = $list->getCurrentPage();
        $this->assign('page',$page);
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
        if($this->store){
            //查看本店商品是否存在备份文件
            //存储路径
            $storePath = realpath(config('upload_dir.upload_path')).'/'.config('upload_dir.factory_goods_backup');
            //本厂商店铺备份文件
            $modelStore = new \common\model\Store;

            $config = [
                'where'=>[
                    ['factory_id','=',$this -> store['factory_id']]
                ],
            ];
            $storeList = $modelStore -> getList($config);
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
        }
        return $this->fetch();
    }
    //设置商品排序
    public function setSort(){
        if (request()->isPost()) {
            $data = input();
            if(empty($data['sortData']) && !is_array($data['sortData'])){
                return errorMsg('参数错误');
            }
            $model = new \common\model\Goods;
            $result = $model->isUpdate(true)->saveAll($data['sortData']);
            if (false !== $result) {
                return successMsg('成功');
            }
        }
        if($this->store) {
            return $this -> fetch();
        }

    }
    //上下架设置
    public function setShelf(){
        if (request()->isPost()) {
            $data = input();
            $model = new \common\model\Goods;
            $result = $model->allowField(true)
                ->save($data, ['id' => $data['goodsId'], 'store_id' => $this->store['id']]);
            if (false !== $result) {
                return successMsg('成功');
            }
        }
        if( $this->store) {
            return $this -> fetch();
        }
    }

    //设置商品库存
    public function setInventory(){
        if(request()->isPost()){
            $model = new \common\model\Goods;
            return $result = $model ->setInventory($this->store['id']);
        }
        if($this->store) {
            return $this -> fetch();
        }
    }

    //商品备份
    /**
     * @return array
     */
    public function backup(){
        if(request()->isPost()){
            $model = new \common\model\Goods;
            $config = [
                'where'=>[
                    ['store_id','=',$this->store['id']]
                ],
                'field'=>[
                    'g.id,g.name,g.trait,thumb_img,g.main_img,g.goods_video,g.parameters,g.details_img,
                        g.retail_price,g.retail_price,g.sale_price,g.settle_price'
                ],
            ];
            $goodsList = $model -> getList($config);
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

    //获取备份
    public function getBackup(){
        if(!request()->isGet()){
            return errorMsg('请求方式错误');
        }
        $storeId = (int)input('get.storeId');
        $modelStore = new \common\model\Store;
        if(!$modelStore -> checkStoreExist($storeId,$this -> store['factory_id'])){
            return errorMsg('不存在店铺');
        }
        //存储路径
        $storePath = realpath(config('upload_dir.upload_path')).'/'.config('upload_dir.factory_goods_backup');
        $storeBackupFile = $storePath.$this->store['id'].'.txt';
        $backup = file_get_contents($storeBackupFile);
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

    //获取场景也选择商品
    public function getSceneGoodsList(){
        if(!request()->isGet()){
            return errorMsg('请求方式错误');
        }
        $selectedGoodsList = json_decode(input('get.goods'),true);
        $goodsIds = [];
        foreach ($selectedGoodsList as $key=>$goods){
            $goodsIds[] = $goods['goods_id'];
        }
        $modelGoods = new \common\model\Goods;
        $config = [
            'where'=>[
                ['id','in',$goodsIds]
            ],
            'field'=>[
                'g.id,g.name,g.thumb_img,g.sale_price'
            ],
        ];
        $goodsList = $modelGoods -> getList($config);
        foreach ($goodsList as $k=>$v){
            foreach ($selectedGoodsList as $kk=>$vv){
                if($v['id'] == $vv['goods_id'] ){
                    $goodsList[$k]['special'] = $vv['special'];
                }
            }
        }
        $this -> assign('list',$goodsList);
        return $this -> fetch('list_scene_selected');
    }

    // 获取推文相关的商品
    /**
     * @return array|mixed
     */
    public function getTweetGoodsList(){
        if(!request()->isGet()){
            return errorMsg('请求方式错误');
        }
        $goodsIds = explode(",",rtrim(input('get.goods'), ","));
        $modelGoods = new \common\model\Goods;
        $config = [
            'where'=>[
                ['id','in',$goodsIds]
            ],
            'field'=>[
                'g.id,g.name,g.thumb_img,g.sale_price'
            ],
        ];
        $goodsList = $modelGoods -> getList($config);
        $this -> assign('list',$goodsList);
        return $this -> fetch('list_tweet_selected');
    }
}