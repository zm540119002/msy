<?php
namespace app\weiya_customization_admin\controller;

/**供应商验证控制器基类
 */
class Goods extends Base {

    /*
     *审核首页
     */
    public function manage(){
        return $this->fetch('manage');
    }

    /**
     * @return array
     * 审核
     */
    public function edit(){
        $modelGoods = new \app\weiya_customization_admin\model\Goods();
        if(request()->isPost()){
            print_r(input());exit;
            if( isset($_POST['main_img']) && $_POST['main_img'] ){
                $_POST['main_img'] = moveImgFromTemp(config('upload_dir.weiya_goods'),basename($_POST['main_img']));
            }
            if( isset($_POST['details_img']) && $_POST['details_img'] ){
                $detailArr = explode(',',input('post.details_img','','string'));
                $tempArr = array();
                foreach ($detailArr as $item) {
                    if($item){
                        $tempArr[] = moveImgFromTemp(config('upload_dir.weiya_goods'),basename($item));
                    }
                }
                $_POST['details_img'] = implode(',',$tempArr);
            }
            if(isset($_POST['goodsId']) && intval($_POST['goodsId'])){//修改
                $config = [
                    'g.id' => input('post.goodsId',0,'int'),
                    'g.status' => 0,
                ];
                $goodsInfo = $modelGoods->getInfo($config);
                //删除商品主图
                if($goodsInfo['main_img']){
                    delImgFromPaths($goodsInfo['main_img'],$_POST['main_img']);
                }
                if($goodsInfo['detail_img']){
                    //删除商品详情图
                    $oldImgArr = explode(',',$goodsInfo['detail_img']);
                    $newImgArr = explode(',',$_POST['detail_img']);
                    delImgFromPaths($oldImgArr,$newImgArr);
                }
                $data['create_time'] = time();
                $where = [
                    'id'=>input('post.goodsId',0,'int')
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
            $modelGoodsCategory = new \app\weiya_customization_admin\model\GoodsCategory();
            $allCategoryList = $modelGoodsCategory->getList();
            $this->assign('allCategoryList',$allCategoryList);
            //要修改的商品
            if(isset($_GET['goodsId']) && intval($_GET['goodsId'])){
                $config = [
                    'where' => [
                        'g.status' => 0,
                        'g.id'=>input('get.goodsId',0,'int'),
                    ],
                ];
                $goodsInfo = $modelGoods->getInfo($config);
                $this->assign('goodsInfo',$goodsInfo);
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
        $modelGoods = new \app\weiya_customization_admin\model\Goods();
        $where = array(
            'g.status' => 0,
//            'g.on_off_line' => 1,
        );
        if(isset($_GET['category_id_1']) && intval($_GET['category_id_1'])){
            $where['g.category_id_1'] = input('get.category_id_1',0,'int');
        }
        if(isset($_GET['category_id_2']) && intval($_GET['category_id_2'])){
            $where['g.category_id_2'] = input('get.category_id_2',0,'int');
        }
        if(isset($_GET['category_id_3']) && intval($_GET['category_id_3'])){
            $where['g.category_id_3'] = input('get.category_id_3',0,'int');
        }
        $keyword = input('get.keyword','','string');
        if($keyword){
            $where['_complex'] = array(
                'g.name' => array('like', '%' . trim($keyword) . '%'),
            );
        }
        /**
         *   `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
        `name` varchar(60) NOT NULL DEFAULT '' COMMENT '商品名称',
        `bulk_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '批量价格',
        `sample_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '样品价格',
        `minimum_order_quantity` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '订单起订量',
        `minimum_sample_quantity` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '样品起订量',
        `specification` varchar(1000) NOT NULL DEFAULT '' COMMENT '商品规格',
        `specification_unit` tinyint(3) NOT NULL DEFAULT '0' COMMENT '商品规格的单位',
        `trait` varchar(60) NOT NULL DEFAULT '' COMMENT '商品特点',
        `category_id_1` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '商品分类1',
        `category_id_2` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '商品分类2',
        `category_id_3` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '商品分类3',
        `thumb_img` varchar(100) NOT NULL DEFAULT '' COMMENT '缩略图',
        `main_img` varchar(2000) NOT NULL DEFAULT '' COMMENT '首焦图',
        `goods_video` varchar(255) NOT NULL DEFAULT '' COMMENT '视频',
        `parameters` varchar(1000) NOT NULL DEFAULT '' COMMENT '参数',
        `details_img` varchar(255) NOT NULL DEFAULT '' COMMENT '详情图',
        `tag` varchar(100) NOT NULL DEFAULT '' COMMENT '标签',
        `shelf_status` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '商品上下架标识位 1：下架 2：待审核 3 上架',
        `rq_code_url` varchar(30) NOT NULL DEFAULT '' COMMENT '商品二维码图片',
        `inventory` int(10) NOT NULL DEFAULT '0' COMMENT '库存',
        `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '添加时间',
        `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '修改时间',
        `sort` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
        `status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '状态：0 ：启用 1：禁用 2：删除',
         */
        $field = [

        ];
        $join =[
            ' left join goods_category gc1 on gc1.id = gb.category_id_1 ',
            ' left join goods_category gc2 on gc2.id = gb.category_id_2 ',
            ' left join goods_category gc3 on gc3.id = gb.category_id_3 ',
        ];
        $order = 'gb.sort, gb.id desc';
        $config = [
            'where'=>$where,
            'field'=>[
                'g.id','g.name','g.bulk_price','g.sample_price','g.minimum_order_quantity','g.minimum_sample_quantity',
                'g.trait','g.main_img','g.parameters','g.details_img','g.tag','g.shelf_status','g.create_time','g.category_id_1',
                'g.category_id_2','g.category_id_3','gc1.name as category_name_1'
            ],
            'join' => [
                ['goods_category gc1','gc1.id = g.category_id_1'],
            ],
            'order'=>[
                'g.id'=>'asc',
                'g.sort'=>'desc',
            ],
        ];
        $goodsList = $modelGoods ->pageQuery($config);
        print_r($goodsList);exit;
        $this->assign('goodsList',$goodsList);
        $this->assign('pageList',$goodsList['pageList']);
        return view('list_tpl');
    }


}