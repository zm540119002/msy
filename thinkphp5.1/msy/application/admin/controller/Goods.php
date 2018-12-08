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
                $_POST['main_img'] = moveImgFromTemp(config('upload_dir.weiya_goods'),basename($_POST['main_img']));
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
            $modelGoodsCategory = new \app\admin\model\GoodsCategory();
            $config = [
                'where'=>[
                    'status'=>0
                ]
            ];
            $allCategoryList = $modelGoodsCategory->getList($config);
            $this->assign('allCategoryList',$allCategoryList);
            //要修改的商品
            if(input('?goodsId') && (int)input('goodsId')){
                $config = [
                    'where' => [
                        'g.status' => 0,
                        'g.id'=>input('goodsId',0,'int'),
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
        $modelGoods = new \app\admin\model\Goods();
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
        $config = [
            'where'=>$where,
            'field'=>[
                'g.id','g.name','g.bulk_price','g.sample_price','g.minimum_order_quantity','g.minimum_sample_quantity',
                'g.trait','g.main_img','g.parameters','g.detail_img','g.tag','g.shelf_status','g.create_time','g.category_id_1',
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
        $this->assign('list',$goodsList);
        if($_GET['pageType'] == 'project'){
            return view('goods/goods_project_list_tpl');
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
}