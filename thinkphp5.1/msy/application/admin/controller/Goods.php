<?php
namespace app\admin\controller;

/**供应商验证控制器基类
 */
class Goods extends Base {
    /*
     *审核首页
     */
    public function manage(){
        // 所有项目分类
        $model = new \app\admin\model\GoodsCategory();
        $config = [
            'where'=>[
                'status'=>0
            ]
        ];
        $allCategoryList = $model->getList($config);
        $this->assign('allCategoryList',$allCategoryList);
        return $this->fetch('manage');
    }

    /**
     * @return array
     * 审核
     */
    public function edit(){
        $modelGoods = new \app\admin\model\Goods();
        if(request()->isPost()){
            if( isset($_POST['main_img']) && $_POST['main_img'] ){
                $detailArr = explode(',',input('post.main_img','','string'));
                $tempArr = array();
                foreach ($detailArr as $item) {
                    if($item){
                        $tempArr[] = moveImgFromTemp(config('upload_dir.weiya_goods'),basename($item));
                    }
                }
                $_POST['main_img'] = implode(',',$tempArr);
                //主图第一张为缩略图
                $_POST['thumb_img'] = $tempArr[0];//
            }
            if( isset($_POST['detail_img']) && $_POST['detail_img'] ){
                $detailArr = explode(',',input('post.detail_img','','string'));
                $tempArr = array();
                foreach ($detailArr as $item) {
                    if($item){
                        $tempArr[] = moveImgFromTemp(config('upload_dir.weiya_goods'),basename($item));
                    }
                }
                $_POST['detail_img'] = implode(',',$tempArr);
            }
            
            if(isset($_POST['id']) && intval($_POST['id'])){//修改
                $config = [
                    'where' => [
                        'id' => input('post.id/d'),
                        'status' => 0,
                    ],
                ];
                $goodsInfo = $modelGoods->getInfo($config);
                //删除商品主图
                if($goodsInfo['thumb_img']){
                    delImgFromPaths($goodsInfo['thumb_img'],$_POST['thumb_img']);
                }
                if($goodsInfo['main_img']){
                    //删除商品详情图
                    $oldImgArr = explode(',',$goodsInfo['main_img']);
                    $newImgArr = explode(',',$_POST['main_img']);
                    delImgFromPaths($oldImgArr,$newImgArr);
                }
                if($goodsInfo['detail_img']){
                    //删除商品详情图
                    $oldImgArr = explode(',',$goodsInfo['detail_img']);
                    $newImgArr = explode(',',$_POST['detail_img']);
                    delImgFromPaths($oldImgArr,$newImgArr);
                }
                $data = $_POST;
                $data['update_time'] = time();
                $where = [
                    'id'=>input('post.id/d')
                ];
                $result = $modelGoods -> allowField(true) -> save($data,$where);
                if(false === $result){
                    return errorMsg('失败');
                }
            }else{//新增
                $data = $_POST;
                $data['create_time'] = time();
                $result = $modelGoods -> allowField(true) -> save($data);
                if(!$result){
                    return errorMsg('失败');
                }
            }
            return successMsg('成功');
        }else{
           // 所有商品分类
            $modelGoodsCategory = new \app\admin\model\GoodsCategory();
            $config = [
                'where'=>[
                    'status'=>0
                ]
            ];
            $allCategoryList = $modelGoodsCategory->getList($config);
            $this->assign('allCategoryList',$allCategoryList);
            //要修改的商品
            if(input('?id') && (int)input('id')){
                $config = [
                    'where' => [
                        'g.status' => 0,
                        'g.id'=>input('id',0,'int'),
                    ],
                ];
                $goodsInfo = $modelGoods->getInfo($config);
                $this->assign('info',$goodsInfo);
            }
            //单位
            $this->assign('unitList',config('custom.unit'));
            return $this->fetch();
       }
    }

    /**
     *  分页查询
     */
    public function getList(){
        $modelGoods = new \app\admin\model\Goods();
        $where = [];
        $where[] = ['g.status','=',0];
        if(isset($_GET['category_id_1']) && intval($_GET['category_id_1'])){
            $where[] = ['g.category_id_1','=',input('get.category_id_1',0,'int')];
        }
        if(isset($_GET['category_id_2']) && intval($_GET['category_id_2'])){
            $where[] = ['g.category_id_2','=',input('get.category_id_2',0,'int')];
        }
        if(isset($_GET['category_id_3']) && intval($_GET['category_id_3'])){
            $where[] = ['g.category_id_3','=',input('get.category_id_3',0,'int')];
        }
        $keyword = input('get.keyword','','string');
        if($keyword){
            $where[] = ['g.name','like', '%' . trim($keyword) . '%'];
        }
        $config = [
            'where'=>$where,
            'field'=>[
                'g.id','g.name','g.bulk_price','g.sample_price','g.sort',
                'g.thumb_img','g.shelf_status','g.create_time','g.category_id_1',
                'gc1.name as category_name_1'
            ],
            'join' => [
                ['goods_category gc1','gc1.id = g.category_id_1'],
            ],
            'order'=>[
                'g.sort'=>'desc',
                'g.id'=>'desc',
            ],
        ];
        $list = $modelGoods ->pageQuery($config);
        $this->assign('list',$list);
        if($_GET['pageType'] == 'layer'){
            return view('goods/list_layer_tpl');
        }
        if($_GET['pageType'] == 'manage'){
            return view('goods/list_tpl');
        }
    }

    /**
     * @return array|mixed
     * 删除
     */
    public function del(){
        if(!request()->isPost()){
            return config('custom.not_post');
        }
        $model = new \app\admin\model\Goods();
        $id = input('post.id/d');
        if(input('?post.id') && $id){
            $condition = [
                ['id','=',$id]
            ];
        }
        if(input('?post.ids')){
            $ids = input('post.ids/a');
            $condition = [
                ['id','in',$ids]
            ];
        }
        return $model->del($condition);
    }


    /**
     * 上下架
     */
    public function setShelfStatus(){
        if(!request()->isPost()){
            return config('custom.not_post');
        }
        $model = new \app\admin\model\Goods();
        $id = input('post.id/d');
        if(!input('?post.id') && !$id){
             return errorMsg('失败');
        }
        $rse = $model->where(['id'=>input('post.id/d')])->setField(['shelf_status'=>input('post.shelf_status/d')]);
        if(!$rse){
            return errorMsg('失败');
        }
        return successMsg('成功');
    }

    //生成商品二维码
    /**
     * @return array
     */
    public function generateGoodsQRcode(){
        if(request()->isPost()){
            $id = input('post.id/d');
            $config = [
                'where'=>[
                    ['id','=',$id],
                    ['status','=',0]
                ]
            ];
            $model = new \app\admin\model\Goods();
            $info = $model -> getInfo($config);
            $oldQRCodes = $info['rq_code_url'];
            $uploadPath = realpath( config('upload_dir.upload_path')) . '/';
            $url = request()->domain().'/index.php/admin/Goods/preview/id/'.$id;
            $newRelativePath = config('upload_dir.weiya_goods');
            $shareQRCodes = createLogoQRcode($url,$newRelativePath);
            $init = [
                'save_path'=>$newRelativePath,   //保存目录  ./uploads/compose/goods....
                'title'=>'维雅生物药妆',
                'slogan'=>'领先的品牌定制平台',
                'name'=> $info['headline'],
                'specification'=> $info['specification'],
                'money'=>'￥'.$info['bulk_price'].' 元',
                'logo_img'=> request()->domain().'/static/common/img/ucenter_logo.png', // 460*534
                'goods_img'=> $uploadPath.$info['thumb_img'], // 460*534
                'qrcode'=>$uploadPath.$shareQRCodes, // 120*120
                'font'=>'./static/font/simhei.ttf',   //字体
            ];
            $res =  $this->compose($init);
            if($res['status'] == 1){
                $newQRCodes = $res['info'];
                $res= $model->where(['id'=>$id])->setField(['rq_code_url'=>$newQRCodes]);
                if(false === $res){
                    return errorMsg('失败');
                }
                unlink($uploadPath.$shareQRCodes);
                if(!empty($oldQRCodes)){
                    unlink($uploadPath.$oldQRCodes);
                }
                return successMsg($newQRCodes);
            }else{
                return successMsg('失败',$res['info']);
            }
        }
    }

    /**
     * 添加商品相关推荐商品
     * @return array|mixed
     * @throws \Exception
     */
    public function addRecommendGoods(){
        if(request()->isPost()){
            $model = new \app\admin\model\RecommendGoods();
            $data = input('post.selectedIds/a');
            $condition = [
                ['goods_id','=',$data[0]['goods_id']]
            ];
            $model->startTrans();
            $rse = $model -> del($condition,$tag=false);
            if(false === $rse){
                $model->rollback();
                return errorMsg('失败');
            }
            $res = $model->allowField(true)->saveAll($data)->toArray();
            if (!count($res)) {
                $model->rollback();
                return errorMsg('失败');
            }
            $model -> commit();
            return successMsg('成功');

        }else{
            if(!input('?id') || !input('id/d')){
                $this ->error('参数有误',url('manage'));
            }
            // 所有商品分类
            $model = new \app\admin\model\GoodsCategory();
            $config = [
                'where'=>[
                    'status'=>0
                ]
            ];
            $allCategoryList = $model->getList($config);
            $this->assign('allCategoryList',$allCategoryList);
            $id = input('id/d');
            $this->assign('id',$id);
            return $this->fetch();
        }
    }

    /***
     * 获取项目相关商品
     * @return array|\think\response\View
     */
    public function getProjectGoods(){
        if(!request()->get()){
            return errorMsg('参数有误');
        }
        if(!input('?get.projectId') || !input('get.projectId/d')){
            return errorMsg('参数有误');
        }
        $projectId = input('get.projectId/d');
        $model = new \app\admin\model\ProjectGoods();
        $config = [
            'where' => [
                ['pg.project_id','=',$projectId],
            ],'join' => [
                ['goods g','g.id = pg.goods_id','left'],
            ],'field' => [
                'g.id','g.thumb_img','g.name',
            ],

        ];
        $list = $model -> getList($config);
        $this->assign('list',$list);
        return view('goods/selected_list');
    }

    /**
     * 获取 商品相关推荐商品
     * @return array|\think\response\View
     */
    public function getRecommendGoods(){
        if(!request()->get()){
            return errorMsg('参数有误');
        }
        if(!input('?get.goodsId') || !input('get.goodsId/d')){
            $this ->error('参数有误');
        }
        $goodsId = input('get.goodsId/d');
        $model = new \app\admin\model\RecommendGoods();
        $config = [
            'where' => [
                ['rg.goods_id','=',$goodsId],
            ],'join' => [
                ['goods g','g.id = rg.goods_id','left'],
            ],'field' => [
                'g.id','g.thumb_img','g.name',
            ],

        ];
        $list = $model -> getList($config);
        $this->assign('list',$list);
        return view('goods/selected_list');
    }

    /**
     * @return mixed
     * 商品预览
     */
    public function preview(){
        if(!input('?id') || !input('id/d')){
            $this ->error('参数有误');
        }
        $id = input('id/d');
        $model = new \app\admin\model\Goods();
        $config = [
            'where'=>[
                ['g.id','=',$id]
            ],
            'field'=>[
                'g.id','g.name','g.headline','g.minimum_order_quantity','g.minimum_sample_quantity','g.bulk_price','g.sample_price',
                'g.specification','g.specification','g.specification_unit','g.intro','g.parameters','g.main_img','g.thumb_img','g.shelf_status','g.create_time','g.category_id_1',
                'g.detail_img','g.tag','gc1.name as category_name_1','g.purchase_unit'
            ],
            'join' => [
                ['goods_category gc1','gc1.id = g.category_id_1'],
            ],
        ];
        $info = $model ->getInfo($config);
        $info['main_img'] = explode(",",rtrim($info['main_img'], ","));
        $info['detail_img'] = explode(",",rtrim($info['detail_img'], ","));
        $info['tag'] = explode(",",rtrim($info['tag'], ","));
        $this ->assign('info',$info);
        return $this->fetch();
    }


    /**合成商品图片
     *
     * @param array $config 合成图片参数
     * @return $img->path 合成图片的路径
     *
     */
    public function compose(array $config=[])
    {
        $init = $config;
        $logoImg = $this->imgInfo($init['logo_img']);
        $goodsImg = $this->imgInfo($init['goods_img']);
        $qrcode = $this->imgInfo($init['qrcode']);
        if( !$logoImg || !$goodsImg || !$qrcode){
            return errorMsg('提供的图片问题');
        }
        $im = imagecreatetruecolor(480, 780);  //图片大小
        $color = imagecolorallocate($im, 240, 255, 255);
        $text_color = imagecolorallocate($im, 0, 0, 0);
        imagefill($im, 0, 0, $color);
        imagettftext($im, 20, 0, 100, 35, $text_color, $init['font'], $init['title']); //XX官方旗舰店
        imagettftext($im, 16, 0, 100, 60, $text_color, $init['font'], $init['slogan']);   //标语
        imagettftext($im, 15, 0, 20, 670, $text_color, $init['font'], $init['money']); //金额
        imagettftext($im, 6, 0,  200, 670, $text_color, $init['font'], $init['specification']); //规格
        imagettftext($im, 12, 0, 20, 700, $text_color, $init['font'], $init['name']); //说明
        imagecopyresized($im, $logoImg['obj'], 10, 10, 0, 0, 60, 55, $logoImg['width'], $logoImg['height'] );  //平台logo
        imagecopyresized($im, $goodsImg['obj'], 10, 106, 0, 0, 460, 534, $goodsImg['width'], $goodsImg['height']);  //商品
        imagecopyresized($im, $qrcode['obj'], 350, 650, 0, 0, 120, 120, $qrcode['width'], $qrcode['height'] );  //二维
        $dir = config('upload_dir.upload_path').'/'.$init['save_path'].'compose/';
        if(!is_dir($dir)){
            mkdir($dir, 0777, true);
        }
        $filename = generateSN(5).'.jpg';
        $file = $dir.$filename;
        if( !imagejpeg($im, $file, 90) ){
            return errorMsg('合成图片失败');
        }
        imagedestroy($im);
        return  successMsg($init['save_path'].'compose/'.$filename);
    }

    private function imgInfo($path)
    {
        $info = getimagesize($path);
        //检测图像合法性
        if (false === $info) {
            return false; //图片不合法
        }
        if($info[2]>3){
            return false; //不支持此图片类型
        }
        $type = image_type_to_extension($info[2], false);
        $fun = "imagecreatefrom{$type}";

        //返回图像信息
        if(!$fun) return false;
        return [
            'width'  => $info[0],
            'height' => $info[1],
            'type'   => $type,
            'mime'   => $info['mime'],
            'obj'    => $fun($path),
        ];
    }
}