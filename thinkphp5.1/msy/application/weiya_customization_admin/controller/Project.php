<?php
namespace app\weiya_customization_admin\controller;

/**供应商验证控制器基类
 */
class Project extends Base {

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
        $model = new \app\weiya_customization_admin\model\Project();
        $modelProjectGoods = new \app\weiya_customization_admin\model\ProjectGoods();
        if(request()->isPost()){
            if( isset($_POST['main_img']) && $_POST['main_img'] ){
                $_POST['main_img'] = moveImgFromTemp(config('upload_dir.weiya_project'),basename($_POST['main_img']));
            }
            if( isset($_POST['detail_img']) && $_POST['detail_img'] ){
                $detailArr = explode(',',input('post.detail_img','','string'));
                $tempArr = array();
                foreach ($detailArr as $item) {
                    if($item){
                        $tempArr[] = moveImgFromTemp(config('upload_dir.weiya_project'),basename($item));
                    }
                }
                $_POST['detail_img'] = implode(',',$tempArr);
            }
            $data = $_POST;

            if(isset($_POST['projectId']) && intval($_POST['projectId'])){//修改
                $config = [
                    'g.id' => input('post.projectId',0,'int'),
                    'g.status' => 0,
                ];
                $goodsInfo = $model->getInfo($config);
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
                $where = [
                    'id'=>input('post.projectId',0,'int')
                ];
                $data['update_time'] = time();
                $result = $model -> allowField(true) -> save($data,$where);
                if(false === $result){
                    return errorMsg('失败');
                }
            }else{//新增
                $model->startTrans();
                $data = $_POST;
                $data['create_time'] = time();
                $result = $model -> allowField(true) -> save($data);
                if(!$result){
                    $model ->rollback();
                    return errorMsg('失败');
                }
                $projectId = $model->getAttr('id');
                $goodsIds = [
                  '5','6','7','8'
                ];

                foreach ($goodsIds as $k=>$v){
                    $projectGoods[$k]['project_id'] =  $projectId;
                    $projectGoods[$k]['goods_id'] =  $v;
                }
                $result = $modelProjectGoods->allowField(true) -> saveAll($projectGoods);
                if (!count($result)) {
                    $model->rollback();
                    return errorMsg('失败');
                }
            }
            $model -> commit();
            return successMsg('成功');
        }else{
           // 所有商品分类
            $modelGoodsCategory = new \app\weiya_customization_admin\model\GoodsCategory();
            $allCategoryList = $modelGoodsCategory->getList();
            $this->assign('allCategoryList',$allCategoryList);
            //要修改的商品
            if(input('?projectId') && (int)input('projectId')){
                $config = [
                    'where' => [
                        'g.status' => 0,
                        'g.id'=>input('projectId',0,'int'),
                    ],
                ];
                $projectInfo = $model->getInfo($config);
                $this->assign('projectInfo',$projectInfo);
            }
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
        $this->assign('goodsList',$goodsList);
        $this->assign('pageList',$goodsList['pageList']);
        return view('list_tpl');
    }


}