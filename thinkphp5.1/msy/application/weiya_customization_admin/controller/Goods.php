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
//        $model = D('Goods');
//        if(IS_POST){
//            if( isset($_POST['main_img']) && $_POST['main_img'] ){
//                $_POST['main_img'] = moveImgFromTemp(C('GOODS_MAIN_IMG'),basename($_POST['main_img']));
//            }
//            if( isset($_POST['detail_img']) && $_POST['detail_img'] ){
//                $detailArr = explode(',',I('post.detail_img','','string'));
//                $tempArr = array();
//                foreach ($detailArr as $item) {
//                    if($item){
//                        $tempArr[] = moveImgFromTemp(C('GOODS_DETAIL_IMG'),basename($item));
//                    }
//                }
//                $_POST['detail_img'] = implode(',',$tempArr);
//            }
//            if(isset($_POST['goodsId']) && intval($_POST['goodsId'])){//修改
//                $where = array(
//                    'g.id' => I('post.goodsId',0,'int'),
//                );
//                $goodsInfo = $model->selectGoods($where);
//                $goodsInfo = $goodsInfo[0];
//                //删除商品主图
//                if($goodsInfo['main_img']){
//                    $this->delImgFromPaths($goodsInfo['main_img'],$_POST['main_img']);
//                }
//                if($goodsInfo['detail_img']){
//                    //删除商品详情图
//                    $oldImgArr = explode(',',$goodsInfo['detail_img']);
//                    $newImgArr = explode(',',$_POST['detail_img']);
//                    $this->delImgFromPaths($oldImgArr,$newImgArr);
//                }
//                $_POST['update_time'] = time();
//                $res = $model->saveGoods();
//            }else{//新增
//                $_POST['create_time'] = time();
//                $res = $model->addGoods();
//            }
//            $this->ajaxReturn($res);
//        }else{
            $modelGoodsCategory = new \app\weiya_customization_admin\model\GoodsCategory();

//            所有商品分类
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
//            $modelUnit = D('Unit');
//            $unitList = $modelUnit->selectUnit();
//            $this->assign('unitList',$unitList);

            return $this->fetch();
//        }
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
            $where['g.category_id_1'] = I('get.category_id_1',0,'int');
        }
        if(isset($_GET['category_id_2']) && intval($_GET['category_id_2'])){
            $where['g.category_id_2'] = I('get.category_id_2',0,'int');
        }
        if(isset($_GET['category_id_3']) && intval($_GET['category_id_3'])){
            $where['g.category_id_3'] = I('get.category_id_3',0,'int');
        }
        $keyword = input('get.keyword','','string');
        if($keyword){
            $where['_complex'] = array(
                'g.name' => array('like', '%' . trim($keyword) . '%'),
            );
        }
//        $field = array(
//            'g.id','g.name','g.category_id_1','g.category_id_2','g.category_id_3','g.inventory','g.sort', 'g.price',
//            'g.single_specification','g.package_num','g.package_unit','g.purchase_unit',
//            'gc1.id category_id_1','gc1.name category_name_1','gc2.id category_id_2',
//            'gc2.name category_name_2','gc3.id category_id_3','gc3.name category_name_3',
//        );
//        $join = array(
//            ' left join goods_category gc1 on gc1.id = gb.category_id_1 ',
//            ' left join goods_category gc2 on gc2.id = gb.category_id_2 ',
//            ' left join goods_category gc3 on gc3.id = gb.category_id_3 ',
//        );
//        $order = 'gb.sort, gb.id desc';
//        $config = [
//            'where'=>$where,
//            'field'=>$field,
//            'join'=>$join,
//            'order'=>$order,
//        ];
        $goodsList = $modelGoods ->pageQuery();
        $this->assign('goodsList',$goodsList);
        $this->assign('pageList',$goodsList['pageList']);
        return view('list_tpl');
    }

    /**
     * @return array
     * 审核
     */
    public function audit(){
        if(!request()->isPost()){
            return errorMsg('请求方式错误');
        }
        $id = (int)input('post.id');
        if(!$id){
            return errorMsg('参数错误');
        }
        $model = new \app\index_admin\model\Goods;
        return $model -> audit();
    }

}