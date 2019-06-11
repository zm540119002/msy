<?php
namespace app\index_admin\controller;

/**供应商验证控制器基类
 */
class Goods extends Base {
    /*
     *审核首页
     */
    public function manage(){

        // 所有项目分类
        $model = new \app\index_admin\model\GoodsCategory();
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
     * 编辑
     */
    public function edit(){
        $modelGoods = new \app\index_admin\model\Goods();
        if(request()->isPost()){
            if( isset($_POST['main_img']) && $_POST['main_img'] ){
                $detailArr = explode(',',input('post.main_img','','string'));
                $tempArr = array();
                foreach ($detailArr as $item) {
                    if($item){
                        $tempArr[] = moveImgFromTemp(config('upload_dir.goods'),$item);
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
                        $tempArr[] = moveImgFromTemp(config('upload_dir.goods'),$item);
                    }
                }
                $_POST['detail_img'] = implode(',',$tempArr);
            }
            if( isset($_POST['goods_video']) && $_POST['goods_video'] ){
                $_POST['goods_video'] = moveImgFromTemp(config('upload_dir.goods'),$_POST['goods_video']);
            }

            // 选中的店铺类型 十进制
            $_POST['belong_to'] = bindec(strrev(implode(input('post.belong_to/a'))));
            $_POST['specification'] = trim(input('post.specification/s'));
            $_POST['purchase_specification_description'] = trim(input('post.purchase_specification_description/s'));

            if(isset($_POST['id']) && intval($_POST['id'])){//修改
                $config = [
                    'where' => [
                        'id' => input('post.id/d'),
                        'status' => 0,
                    ],
                ];

                $data = $_POST;
                $data['update_time'] = time();
                $where = [
                    'id'=>input('post.id/d')
                ];

                $result = $modelGoods -> allowField(true) -> save($data,$where);
                if(false === $result){
                    return errorMsg('失败');
                }

                $info = $modelGoods->getInfo($config);
                // 删除旧文件
                if($info['goods_video']){
                    delImgFromPaths($info['goods_video'],$_POST['goods_video']);
                }
                if($info['main_img']){
                    $oldImgArr = explode(',',$info['main_img']);
                    $newImgArr = explode(',',$_POST['main_img']);
                    delImgFromPaths($oldImgArr,$newImgArr);
                }
                if($info['detail_img']){
                    $oldImgArr = explode(',',$info['detail_img']);
                    $newImgArr = explode(',',$_POST['detail_img']);
                    delImgFromPaths($oldImgArr,$newImgArr);
                }

                $data['id'] = input('post.id/d');
                $list[] = $data;
                $this->generateQRcode($list);
            }else{//新增
                $data = $_POST;
                $data['create_time'] = time();
                $result = $modelGoods -> allowField(true) -> save($data);
                if(!$result){
                    return errorMsg('失败');
                }
                $data['id'] = $modelGoods->getAttr('id');
                $list[] = $data;
                $this->generateQRcode($list);
            }

            return successMsg('成功');
        }else{
           // 所有商品分类
            $modelGoodsCategory = new \app\index_admin\model\GoodsCategory();
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

                // 选中的店铺
                $goodsInfo['belong_to'] = strrev(decbin($goodsInfo['belong_to']));

                $this->assign('info',$goodsInfo);
            }


            $config = [
                'field' => [
                    'sort'
                ], 'order'=>[
                    'sort'=>'desc',
                ],
            ];
            $info = $modelGoods->getInfo($config);
            $sort_num = $info['sort']+1;
            $this->assign('sort_num',$sort_num);

            //单位
            $this->assign('unitList',config('custom.unit'));
            return $this->fetch();
       }
    }

    /**
     *  分页查询
     */
    public function getList(){

        $model = new \app\index_admin\model\Goods();

        $where[] = ['g.status','=',0];

        if($category_id_1 = input('category_id_1/d')){
            $where[] = ['g.category_id_1','=',$category_id_1];
        }
        if($category_id_2 = input('category_id_2/d')){
            $where[] = ['g.category_id_2','=',$category_id_2];
        }
        if($category_id_3 = input('category_id_3/d')){
            $where[] = ['g.category_id_3','=',$category_id_3];
        }
        if($belong_to = input('belong_to/d')){ // 经过特别处理
            $where[] = ['g.belong_to','=',$belong_to];
        }
        if($shelf_status = input('shelf_status/d')){
            $where[] = ['g.shelf_status','=',$shelf_status];
        }

        $keyword = input('get.keyword','','string');
        if($keyword){
            $where[] = ['g.name','like', '%' . trim($keyword) . '%'];
        }
        $ids = input('get.ids/s');
        if($ids){
            $ids = addslashes(trim($ids));
            $where['where'][] = ['g.id','exp',"NOT IN ($ids)"];
        }

        $config = [
            'where'=>$where,
            'field'=>[
                'g.id','g.name','g.bulk_price','g.sample_price','g.sort','g.is_selection',
                'g.thumb_img','g.shelf_status','g.create_time','g.rq_code_url','g.belong_to','g.goods_code'
//                'g.category_id_1',
//                'gc1.name as category_name_1'
            ],
//            'join' => [
//                ['goods_category gc1','gc1.id = g.category_id_1'],
//            ],
            'order'=>[
                'g.sort'=>'desc',
                'g.id'=>'desc',
            ],
        ];

        $list = $model ->pageQuery($config);

        $this->assign('list',$list);
        if($_GET['pageType'] == 'layer'){
            return view('goods/list_layer_tpl_copy');
            //return view('goods/list_layer_tpl');
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
        $model = new \app\index_admin\model\Goods();
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
     * 单字段设置
     */
    public function setInfo(){
        if(!request()->isPost()){
            return config('custom.not_post');
        }

        $id  = input('post.id/d');
        if (!$id){
            return errorMsg('失败');
        }

        $info= array();
        // 上下架
        if (input('?shelf_status')){
            $shelf_status = input('post.shelf_status/d')==3 ? 1 : 3 ;

            $info = ['shelf_status'=>$shelf_status];
        }
        // 精选
        if (input('?is_selection')){
            //$is_selection = $is_selection==1 ? 0 : 1 ;
            $info = ['is_selection'=>input('post.is_selection/d')];
        }

        $model = new \app\index_admin\model\Goods();
        $rse = $model->where(['id'=>$id])->setField($info);

        if(!$rse){
            return errorMsg('失败');
        }
        return successMsg('成功');
    }



    /**
     * 增加各关联表下的商品 -通用方法
     */
    public function addRelationGoods(){
        if(!request()->isPost()){
            return config('custom.not_post');
        }

        if(!$data=input('post.selectedIds/a'))  return errorMsg('参数有误');

        $relation=input('post.relation/d');

        // custom.php relation_type
        switch($relation){
            case config('custom.relation_type.scene'):
                $model = new \app\index_admin\model\SceneGoods();
                $condition = [['scene_id','=',$data[0]['scene_id']]];
                break;
            case config('custom.relation_type.project'):
                $model = new \app\index_admin\model\ProjectGoods();
                $condition = [['project_id','=',$data[0]['project_id']]];
                break;
            case config('custom.relation_type.promotion'):
                $model = new \app\index_admin\model\PromotionGoods();
                $condition = [['promotion_id','=',$data[0]['promotion_id']]];
                break;
            case config('custom.relation_type.sort'):
                $model = new \app\index_admin\model\SortGoods();
                $condition = [['sort_id','=',$data[0]['sort_id']]];
                break;
            default:
                return errorMsg('参数有误');
        }

        $model->startTrans();
        $rse = $model -> del($condition,false);

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
    }

    /***
     * 获取各关联表下的商品 -通用方法
     * @return array|\think\response\View
     */
    public function getRelationGoods(){
        if(!request()->get()){
            return errorMsg('参数有误');
        }
        if(!$id = input('get.id/d')){
            return errorMsg('参数有误');
        };
        $relation = input('get.relation/d');
        // custom.php relation_type
        switch($relation){
            case config('custom.relation_type.scene'):
                $model = new \app\index_admin\model\SceneGoods();
                $field_id = 'sg.scene_id';
                $goods_id = 'sg.goods_id';
                break;
            case config('custom.relation_type.project'):
                $model = new \app\index_admin\model\ProjectGoods();
                $field_id = 'pg.project_id';
                $goods_id = 'pg.goods_id';
                break;
            case config('custom.relation_type.promotion'):
                $model = new \app\index_admin\model\PromotionGoods();
                $field_id = 'pg.promotion_id';
                $goods_id = 'pg.goods_id';
                break;
            case config('custom.relation_type.sort'):
                $model = new \app\index_admin\model\SortGoods();
                $field_id = 'sg.sort_id';
                $goods_id = 'sg.goods_id';
                break;
            default:
                return errorMsg('参数有误');
        }

        $config = [
            'where' => [
                [$field_id,'=',$id],['g.status','=', 0], ['g.shelf_status','=', 3]
            ],'join' => [
                ['goods g','g.id = '.$goods_id,'left'],
            ],'field' => [
                'g.id','g.thumb_img','g.name',
            ],
        ];

        $list = $model -> getList($config);
        $this->assign('list',$list);
        return view('goods/selected_list');
    }

    /*
     * 添加商品相关推荐商品
     * @return array|mixed
     * @throws \Exception
     */
    public function addRecommendGoods(){
        if(request()->isPost()){
            $model = new \app\index_admin\model\RecommendGoods();
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
            $model = new \app\index_admin\model\GoodsCategory();
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

    /**获取推荐商品
     * @return array|\think\response\View
     */
    public function getRecommendGoods(){
        if(!request()->isGet()){
            return errorMsg('请求方式错误');
        }
        if(!input('?get.goods_id') || !input('get.goods_id/d')){
            $this ->error('参数有误');
        }
        $goodsId = input('get.goods_id/d');
        //相关推荐商品
        $modelRecommendGoods = new \app\index\model\RecommendGoods();
        $config =[
            'where' => [
                ['rg.status', '=', 0],
                ['rg.goods_id', '=', $goodsId],
            ],'field'=>[
                'g.id ','g.headline','g.thumb_img','g.bulk_price','g.specification','g.minimum_order_quantity',
                'g.minimum_sample_quantity','g.increase_quantity','g.purchase_unit','g.name'
            ],'join'=>[
                ['goods g','g.id = rg.recommend_goods_id','left']
            ]
        ];
        $list= $modelRecommendGoods->getList($config);
        $this->assign('list',$list);
        $type = input('get.type');
        if($type == 'add'){
            return view('goods/selected_list');
        }
        if($type == 'preview'){
            return view('goods/recommend_list_tpl');
        }

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
        $model = new \app\index_admin\model\Goods();
        $config = [
            'where'=>[
                ['g.id','=',$id]
            ],
            'field'=>[
                'g.id','g.name','g.headline','g.minimum_order_quantity','g.minimum_sample_quantity','g.bulk_price','g.sample_price',
                'g.specification','g.specification','g.specification_unit','g.intro','g.parameters','g.main_img','g.thumb_img','g.shelf_status','g.create_time','g.category_id_1',
                'g.detail_img','g.tag','g.purchase_unit','g.rq_code_url',
//                'gc1.name as category_name_1',
            ],
//            'join' => [
//                ['goods_category gc1','gc1.id = g.category_id_1'],
//            ],
        ];
        $info = $model ->getInfo($config);
        $info['main_img'] = explode(",",rtrim($info['main_img'], ","));
        $info['detail_img'] = explode(",",rtrim($info['detail_img'], ","));
        $info['tag'] = explode(",",rtrim($info['tag'], ","));
        $this ->assign('info',$info);
        return $this->fetch();
    }
    //生成商品二维码
    /**
     * @return array
     */
    public function generateGoodsQRcode(){
        if(request()->isPost()){
            $ids = input('post.ids/a');
            $config = [
                'where'=>[
                    ['id','in',$ids],
                    ['status','=',0]
                ],'field'=>[
                    'id','headline','specification','thumb_img','bulk_price','rq_code_url'
                ],
            ];
            $model = new \app\index_admin\model\Goods();
            $list = $model -> getList($config);
            return $this->generateQRcode($list);

        }
    }

    public function generateQRcode($list){
        foreach ($list as $k=>&$info){
            $oldQRCodes = $info['rq_code_url'];
            $uploadPath = realpath( config('upload_dir.upload_path')) . '/';
            $url = request()->domain().'/index.php/Index/Goods/detail/id/'.$info['id'];
            $newRelativePath = config('upload_dir.weiya_goods');
            $shareQRCodes = createLogoQRcode($url,$newRelativePath);
            if(mb_strlen( $info['headline'], 'utf-8')>20){
                $name1 =  mb_substr( $info['headline'], 0, 18, 'utf-8' ) ;
                $name2 =  mb_substr( $info['headline'], 18, 18, 'utf-8' ) ;
            }else{
                $name1 = $info['headline'];
                $name2 = '';
            }
            $init = [
                'save_path'=>$newRelativePath,   //保存目录  ./uploads/compose/goods....
                'title'=>'维雅生物药妆',
                'slogan'=>'领先的品牌定制平台',
                'name1'=> $name1,
                'name2'=> $name2,
                'RMB_logo'=> './static/common/img/RMB_logo.png',
                'money'=>$info['bulk_price'].'元',
                'logo_img'=> request()->domain().'/static/index/img/logo.png', // 460*534
                'goods_img'=> $uploadPath.$info['thumb_img'], // 460*534
                'qrcode'=>$uploadPath.$shareQRCodes, // 120*120
                'font'=>'./static/font/simhei.ttf',   //字体
            ];
            $res =  $this->compose($init);
            if($res['status'] == 1){
                $newQRCodes = $res['info'];
                $model = new \app\index_admin\model\Goods();
                $res= $model->where(['id'=>$info['id']])->setField(['rq_code_url'=>$newQRCodes]);
                if(false === $res){
                    return errorMsg('失败');
                }
                unlink($uploadPath.$shareQRCodes);
                if(!empty($oldQRCodes)){
                    unlink($uploadPath.$oldQRCodes);
                }
//                    return successMsg($newQRCodes);
            }
        }
        if(count($list) == 1){
            return successMsg($newQRCodes);
        }
        return successMsg('成功');
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
        $RMB_logo = $this->imgInfo($init['RMB_logo']);
        if( !$logoImg || !$goodsImg || !$qrcode){
            return errorMsg('提供的图片问题');
        }
        $im = imagecreatetruecolor(480, 780);  //图片大小
        $color = imagecolorallocate($im, 0xFF,0xFF,0xFF);
        $text_color = imagecolorallocate($im, 87, 87, 87);
        $text_color1 = imagecolorallocate($im, 137, 137, 137);
        $red_color = imagecolorallocate($im, 230, 0, 18);
        imagefill($im, 0, 0, $color);
        imagettftext($im, 20, 0, 100, 35, $text_color, $init['font'], $init['title']); //XX官方旗舰店
        imagettftext($im, 16, 0, 100, 60, $text_color1, $init['font'], $init['slogan']);   //标语
        imagettftext($im, 13, 0, 20, 650, $text_color, $init['font'], $init['name1']); //说明
        imagettftext($im, 13, 0, 20, 675, $text_color, $init['font'], $init['name2']); //说明
        imagettftext($im, 20, 0, 50, 730, $red_color, $init['font'], $init['money']); //金额
        imagecopyresized($im, $RMB_logo['obj'], 20, 710, 0, 0, 20, 20, $RMB_logo['width'], $RMB_logo['height'] );  //
        imagecopyresized($im, $logoImg['obj'], 10, 10, 0, 0, 90, 60, $logoImg['width'], $logoImg['height'] );  //平台logo
        imagecopyresized($im, $goodsImg['obj'], 10, 70, 0, 0, 500, 534, $goodsImg['width'], $goodsImg['height']);  //商品
//        imagecopyresized($im, $qrcode['obj'], 350, 630, 0, 0, 120, 120, $qrcode['width'], $qrcode['height'] );  //二维
        imagecopyresized($im, $qrcode['obj'], 330, 630, 0, 0, 140, 140, $qrcode['width'], $qrcode['height'] );  //二维

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

    /**
     * 关联结构商品列表 入口
     */
    public function recommendGoods(){

        return $this->fetch();
    }

    /**
     * 排队已关联的商品
     */
    public function excludeRecommendGoods(){
   /*     if(!request()->get()){
            return errorMsg('参数有误');
        }*/
        if(!$id = input('id/d')){
            return errorMsg('参数有误');
        };
        $relation = input('relation/d');

        $view = 'goods/list_layer_tpl';
        //$view = 'goods/list_tpl';

        switch($relation){
            case config('custom.relation_type.scene'):
                $model = new \app\index_admin\model\SceneGoods();
                $field_id = 'scene_id';
                break;
            case config('custom.relation_type.project'):
                $model = new \app\index_admin\model\ProjectGoods();
                $field_id = 'project_id';
                break;
            case config('custom.relation_type.promotion'):
                $model = new \app\index_admin\model\PromotionGoods();
                $field_id = 'promotion_id';
                break;
            case config('custom.relation_type.sort'):
                $model = new \app\index_admin\model\SortGoods();
                $field_id = 'sort_id';
                break;
            default:
                return errorMsg('参数有误');
        }

        $sql = $model->where($field_id,'=',$id)->field('goods_id')->buildSql();

        $condition = [
            'where' => [
                ['status','=', 0],
                ['shelf_status','=', 3],
                ['id','exp',"NOT IN $sql"],
            ],'field' => [
                'id','thumb_img','name',
            ],
        ];

        $modelGoods = new \app\index_admin\model\Goods();
        $list = $modelGoods->pageQuery($condition);

        $this->assign('list',$list);
        return view($view);
    }


    /**
     * 修改各关联表下的关联商品
     */
    public function RelationGoods(){
        if(!request()->isPost()){
            return config('custom.not_post');
        }

        if(!$data=input('post.selectedIds/a'))  return errorMsg('参数有误');

        $relation=input('post.relation/d');

        // custom.php relation_type
        switch($relation){
            case config('custom.relation_type.scene'):
                $model = new \app\index_admin\model\SceneGoods();
                $condition = [['scene_id','=',$data[0]['scene_id']]];
                break;
            case config('custom.relation_type.project'):
                $model = new \app\index_admin\model\ProjectGoods();
                $condition = [['project_id','=',$data[0]['project_id']]];
                break;
            case config('custom.relation_type.promotion'):
                $model = new \app\index_admin\model\PromotionGoods();
                $condition = [['promotion_id','=',$data[0]['promotion_id']]];
                break;
            case config('custom.relation_type.sort'):
                $model = new \app\index_admin\model\SortGoods();
                $condition = [['sort_id','=',$data[0]['sort_id']]];
                break;
            default:
                return errorMsg('参数有误');
        }

        $model->startTrans();
        $rse = $model -> del($condition,false);

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
    }
}