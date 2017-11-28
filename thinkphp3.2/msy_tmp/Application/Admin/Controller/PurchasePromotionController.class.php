<?php
namespace Admin\Controller;
use Think\Controller;
use Common\Controller\BaseController;
class PurchasePromotionController extends BaseController {
    public function index(){
        $this->display();
    }

    /**
     * 促销套餐列表
     */
    public function bundlingList(){
        //商品的一级分类
        $goodsCategoryList = M('goods_category','','DB_CONFIG2') -> where(array('parent_id' => 0)) -> field('id,name') -> select();
        $this -> goodsCategoryList = $goodsCategoryList;
        $this ->display();
    }

    public function ajaxBundlingList(){
        //套餐列表
        // where条件
        $where = array ();
        if (intval($_GET['category_id']) > 0) {
            $where['category_id'] = array(intval($_GET['category_id']));
        }
        if (trim($_GET['keyword']) != '') {
            $where['name'] = array('like', '%' . trim($_GET['keyword']) . '%');
        }

        $bundlingModel = M('bundling','','DB_CONFIG2'); // 实例化User对象
        $count = $bundlingModel->where($where)->count();    //计算总数
        $page = new \Component\PageAjax($count, 2);
        $bundlingList = $bundlingModel->where($where)->limit($page->firstRow . ',' . $page->listRows)->order('id DESC')->select();
        $pageList =$page->show();
        $this -> bundlingList = $bundlingList;
        $this -> pageList     = $pageList;
        $this ->display();

    }


    public function ajaxGoodsList(){
        //套餐列表
        // where条件
        $category_id_1 = intval($_GET['parent_id_1']);
        $category_id_2 = intval($_GET['parent_id_2']);
        $category_id_3 = intval($_GET['parent_id_3']);
        if ($category_id_1 && $category_id_2 && $category_id_3>0) {
            $where['goods_category_id']   = array($category_id_3);
        }
        if($category_id_1 && $category_id_2 && $category_id_3==0){
            $where['goods_category_id']   = array($category_id_2);
            $where['goods_category_id_2'] = array($category_id_2);
            $where['_logic'] = 'OR';
        }
        if($category_id_1  && $category_id_2==0 && $category_id_3==0){
            $where['goods_category_id']   = array($category_id_1);
            $where['goods_category_id_1'] = array($category_id_1);
            $where['_logic'] = 'OR';
        }
        if(isset($where)){
            $map['_complex'] = $where;
        }
        if (trim($_GET['keyword']) != '') {
            $map['name'] = array('like', '%' . trim($_GET['keyword']) . '%');
        }

        $map['state']  = array('eq',1);
        $goodsModel = M('goods','','DB_CONFIG2'); // 实例化User对象
        $count     = $goodsModel->where($map)->count();    //计算总数
        $page      = new \Component\PageAjax($count, 8);
        $goodsList = $goodsModel->where($map)->limit($page->firstRow . ',' . $page->listRows)->order('id DESC')->select();
        $pageList =$page->show();
        $this -> goodsList = $goodsList;
        $this -> pageList  = $pageList;
        $this ->display();


    }
    //添加修改页面展示
    public function addBundling(){
        if( isset($_GET['bundling_id'] ) && ( $_GET['bundling_id']) ){
            $bundling_id = intval($_GET['bundling_id']);
            if($bundling_id < 0){
                $this -> error('参数错误');
            }
            $condition = array('id' => $bundling_id);
            $bundlingGoodsList = $this -> getBundlingList($condition);
            $this -> bundlingGoodsList = $bundlingGoodsList;

        }
        //商品的一级分类
        $goodsCategoryList = M('goods_category','','DB_CONFIG2') -> where(array('parent_id' => 0)) -> field('id,name') -> select();
        $this -> goodsCategoryList = $goodsCategoryList;
        $this -> display();
    }

    /**
     * 添加修改套餐
     */
    public function addEditBundling(){
        if(IS_POST){
            if(intval($_POST['bundling_price']) < 0 || intval($_POST['upper_limit']) < 0 || intval($_POST['virtual_quantity']) < 0){
                $this->ajaxReturn(errorMsg('输入的价格或数量不能为负数'));
            }
            $bundlingModel = M('bundling','','DB_CONFIG2');
            //开启事务
            $bundlingModel -> startTrans();
            try{
                $newRelativePath = C('BUNDLING_IMG_PATH');
                if( isset($_POST['bundling_image'] ) && $_POST['bundling_image'] ){
                    $_POST['bundling_image'] = moveImgFromTemp($newRelativePath,basename( $_POST['bundling_image']));
                }
                if( isset($_POST['bundling_image1'] ) && $_POST['bundling_image1'] ){
                    $_POST['bundling_image1'] = moveImgFromTemp($newRelativePath,basename( $_POST['bundling_image1']));
                }
                $startTime  = strtotime(  $_POST['start_time'] );
                $endTtime   = strtotime(  $_POST['end_time'] );
                $data = array();
                $data['name']             = $_POST['name'];
                $data['remark']           = $_POST['remark'];
                $data['bundling_price']   = intval($_POST['bundling_price']);
                $data['start_time']       = $startTime;
                $data['end_time']         = $endTtime;
                $data['category_id']      = intval($_POST['category_id']);
                $data['upper_limit']      = intval($_POST['upper_limit']);
                $data['virtual_quantity'] = intval($_POST['virtual_quantity']);
                $data['bundling_intro']   = $_POST['bundling_intro'];
                $data['bundling_image']   = $_POST['bundling_image'];
                $data['bundling_image1']  = $_POST['bundling_image1'];

                $bundlingId = $_POST['bundling_id'];
                if (isset($bundlingId) && !empty($bundlingId)) {
                    //修改套餐
                    $data['updata_time']  = time();
                    $where  = array('id' => intval($bundlingId));
                    $return =$bundlingModel -> where($where) -> save($data);
                    if (false === $return) {
                        throw new \Exception( $this -> ajaxReturn(errorMsg('修改套餐失败')));
                    };
                }else{
                    //增加套餐
                    $data['create_time']  = time();
                    $return = $bundlingModel->add($data);
                    if (!$return) {
                        throw new \Exception( $this -> ajaxReturn(errorMsg('添加套餐失败')));
                    };
                }
                // 插入套餐商品
                $bundlingGoodsModel = M('bundling_goods','','DB_CONFIG2');
                $goodsModel = M('goods','','DB_CONFIG2');
                $data_goods = array();

                //删除
                if (isset($bundlingId) && !empty($bundlingId)) {
                    $result  =  $this->delBundlingGoods(array('bundling_id' => intval($bundlingId)));
                     if(!$result){
                         throw new \Exception( $this->ajaxReturn(errorMsg('删除原来套餐商品失败')));
                     }
                }

                if (!empty($_POST['goods']) && is_array($_POST['goods'])) {
                    foreach ($_POST['goods'] as $key => $val) {
                        // 验证是否为本店铺商品
                        $goods_info = $goodsModel -> where( array('id' => $val['goodsId'] )) -> field('id,name,first_img,price') -> find();
//                    if (empty($goods_info) || $goods_info['store_id'] != $_SESSION['store_id']) {
//                        continue;
//                    }
                        $data = array();
                        $data['bundling_id']         = isset( $bundlingId ) && !empty($bundlingId) ? intval($bundlingId) : $return;
                        $data['goods_id']            = $goods_info['id'];
                        $data['goods_name']          = $goods_info['name'];
                        $data['goods_image']         = $goods_info['first_img'];
                        $data['goods_num']           = $val['goodsNum'];
                        $data['original_goods_price'] = $goods_info['price'];
                        $data['bundling_goods_price'] = $val['goodsPrice'];
                        $data_goods[] = $data;
                    }
                }else{
                    throw new \Exception( $this->ajaxReturn(errorMsg('请添加商品')));
                }

                // 插入数据
                $return = $bundlingGoodsModel->addAll($data_goods);
                if (!$return) {
                    throw new \Exception( $this->ajaxReturn(errorMsg('套餐商品保存失败')));
                };
                $bundlingModel->commit();
                $this->ajaxReturn(successMsg('操作成功'));
            }catch(\Exception $e){
                $bundlingModel->rollback();
                $this->ajaxReturn($e->getMessage());
            }
       }
    }

    /**
     * 删除套餐时删除套餐商品
     */
    public function delBundlingGoods($condition) {
        $list = $this->getBundlingGoodsList($condition, 'goods_id');

        if (empty($list)) {
            return true;
        }
        $bundlingModel = M('bundling_goods','','DB_CONFIG2');
        $result = $bundlingModel -> where($condition) -> delete();
        return $result;
    }

    //获取套餐商品列表
    public function getBundlingGoodsList($condition){
        $bundlingModel = M('bundling_goods','','DB_CONFIG2');
        $result = $bundlingModel -> where($condition)  -> select();
        return $result;
    }
    /**
     * 套餐商品列表
     */
    public function bundlingGoodsList() {
        if(IS_POST){
            $bundlingId = intval($_POST['bundling_id']);
            if($bundlingId < 0){
                $this -> error('套餐ID有错误');
            }
            $bundling_goods = M( 'bundling_goods','','DB_CONFIG2' );
            $bundlingGoodsList =  $bundling_goods->where( array('bundling_id' => $bundlingId ))->select();
            $this -> ajaxReturn( $bundlingGoodsList );
        }
    }

    /**
     * 套餐列表
     */
    public function getBundlingList($condition, $field = '*', $order = 'id asc', $group = '') {
        $bundling = M('bundling','','DB_CONFIG2');
        return  $bundling -> field($field) -> where($condition) -> group($group) -> order($order) -> find();
    }

    /**
     * 删除套餐
     */
    public function delBundling(){
        if(IS_POST){
            $bundlingModel      = M('bundling','','DB_CONFIG2');
            $bundlingGoodsModel = M('bundling_goods','','DB_CONFIG2');
             //开启事务
            $bundlingModel -> startTrans();
            try{
                //批量删除商品
                if(is_array(I('post.bundling_ids')) && !empty(I('post.bundling_ids'))){
                    $result = $bundlingModel -> where(array('id' => array('in', $_POST['bundling_ids']))) -> delete();
                    if(false === $result){
                        throw new \Exception( $this->ajaxReturn(errorMsg('删除套餐基本信息失败')));
                    }
                    $result = $bundlingGoodsModel -> where(array('bundling_id' => array('in', $_POST['bundling_ids']))) -> delete();
                    if(false === $result){
                        throw new \Exception( $this->ajaxReturn(errorMsg('删除套餐商品失败')));
                    }
                }
                //单独删除商品
                if(isset($_POST['bundling_id']) && $_POST['bundling_id']){
                    $result  = $bundlingModel -> where(array('id' => intval($_POST['bundling_id']))) -> delete();
                    if(false === $result){
                        throw new \Exception( $this->ajaxReturn(errorMsg('删除套餐基本信息失败')));
                    }
                    $result  = $bundlingGoodsModel -> where(array('bundling_id' => intval($_POST['bundling_id']))) -> delete();
                    if(false === $result){
                        throw new \Exception( $this->ajaxReturn(errorMsg('删除套餐商品失败')));
                    }
                }
                $bundlingModel -> commit();
                $this -> ajaxReturn( successMsg ('删除套餐成功') );
            }catch ( \Exception $e ){
                $bundlingModel -> rollback();
                $this -> ajaxReturn( $e -> getMessage() );
            }
        }
    }


    //增加修改主题二级页面
    public function addSecondTheme(){
        if(isset($_GET['theme_id']) && $_GET['theme_id']){
            $model = M('SecondTheme','','DB_CONFIG2');
            $theme_id   = intval($_GET['theme_id']);
            $where = array('id' => $theme_id);
            $secondTheme = $model -> where($where) -> find();
            $pieces = explode(",", $secondTheme['theory_img_1']);
            $theory_img_1 = array();
            foreach ($pieces as $v){
                $theory_img_1[] = "<p><img src='/Uploads/$v' title='$v'/></p>";
            }
            $theory_img_1 = implode('',$theory_img_1);
            $this -> theory_img_1 = $theory_img_1;
            $pieces = explode(",", $secondTheme['theory_img_2']);
            $theory_img_2 = array();
            foreach ($pieces as $v){
                $theory_img_2[] = "<p><img src='/Uploads/$v' title='$v'/></p>";
            }
            $theory_img_2 = implode('',$theory_img_2);
            $this -> theory_img_2 = $theory_img_2;
            $video = $secondTheme['video'];

            $video = "<p><video class='edui-upload-video  vjs-default-skin video-js' controls='' preload='none' width='420' height='280' src='$video' data-setup='{}'></video></p>";
            $this -> video =$video;
            $this -> secondTheme = $secondTheme;
        }
        
        //商品的一级分类
        $goodsCategoryList = M('goods_category','','DB_CONFIG2') -> where(array('parent_id' => 0)) -> field('id,name') -> select();
        $this -> goodsCategoryList = $goodsCategoryList;
        $this ->display();
    }
    //增加修改主题二级
    public function addEditSecondTheme(){
       if(IS_POST){

           $rules = array(
               array('name','require','主题名称必须！'),
               array('start_time','require','请选择开始时间'),
               array('end_time','require','请选择结束时间！'),
               array('theme_img','require','请上传主题图片！'),
               array('category_id','require','请选择分类！'),
           );
           $model = M('SecondTheme','','DB_CONFIG2');
           $res = $model->validate($rules)->create();
           if(!$res){
               $this->error($model->getError());
           }
           if(!isset($_POST['goods']) && empty($_POST['goods'])){
               $this->error('请添加相关商品');
           }
           if(!isset($_POST['theory_img_1']) && empty($_POST['theory_img_1'])){
               $this->error('请上传原理图片一');
           }
           if(!isset($_POST['theory_img_2']) && empty($_POST['theory_img_2'])){
               $this->error('请上传原理图片二');
           }

           $newRelativePath = C('THEME_IMG_PATH');
           if( isset($_POST['theme_img'] ) && $_POST['theme_img'] ){
               $_POST['theme_img'] = moveImgFromTemp($newRelativePath,basename( $_POST['theme_img']));
           }

           if(isset($_POST['theory_img_1']) && $_POST['theory_img_1'] ){
               $post_theory_img_1 = array();
               foreach ($_POST['theory_img_1'] as $val){
                   $url = moveImgFromTemp($newRelativePath,basename($val));
                   $post_theory_img_1[] = $url;
               }
           }
           if(isset($_POST['theory_img_2']) && $_POST['theory_img_2'] ){
               $post_theory_img_2 = array();
               foreach ($_POST['theory_img_2'] as $val){
                   $url = moveImgFromTemp($newRelativePath,basename($val));
                   $post_theory_img_2[] = $url;
               }
           }
           if( isset($_POST['video'] ) && $_POST['video'] ){
               $_POST['video'] = moveImgFromTemp($newRelativePath,basename( $_POST['video']));
           }

           $_POST['start_time']  = strtotime(  $_POST['start_time'] );
           $_POST['end_time']    = strtotime(  $_POST['end_time'] );
           
           $_POST['goods']=json_encode($_POST['goods']);
           $_POST['instrument']=json_encode($_POST['instrument']);

           if( isset($_POST['theme_id']) && intval($_POST['theme_id']) ){//修改

               $where['id'] = intval($_POST['theme_id']);
               //删除已被修改的图片或视频
               $secondTheme = $model -> where($where) -> find();
               if($_POST['theme_img'] !== $secondTheme['theme_img']){
                   unlink('./Uploads/'.$secondTheme['theme_img']);
               }
               if($_POST['video'] !== $secondTheme['video']){
                   unlink('./Uploads/'.$secondTheme['video']);
               }
               $theory_img_1 = explode(",", $secondTheme['theory_img_1']);
               $diff_theory_img_1 = array_diff($theory_img_1,$post_theory_img_1);
               foreach ($diff_theory_img_1 as $k => $v){
                    unlink('./Uploads/'.$v);
               }

               $theory_img_2 = explode(",", $secondTheme['theory_img_2']);
               $diff_theory_img_2 = array_diff($theory_img_2,$post_theory_img_2);
               foreach ($diff_theory_img_2 as $k => $v){
                   unlink('./Uploads/'.$v);
               }

                   //修改
               $_POST['theory_img_1'] = implode(',',$post_theory_img_1);
               $_POST['theory_img_2'] = implode(',',$post_theory_img_2);
               $_POST['update_time']=time();

               $result = $model->where($where) ->save($_POST);
                  if(false===$result){
                       $this->error('修改失败');
                  }
                $this->success('修改成功');

           }else{//增加
               $_POST['theory_img_1'] = implode(',',$post_theory_img_1);
               $_POST['theory_img_2'] = implode(',',$post_theory_img_2);
               $_POST['create_time']=time();
               $result = $model ->add($_POST);
               if($result){
                   $this->success('添加成功');
               }else{
                   $this->error('添加失败');
               }
           }
       }
    }



    /**
     * 主题采购相关商品和仪器列表
     */
    public function themeGoodsList() {
        if(IS_POST){
            $themeId = intval($_POST['theme_id']);
            if($themeId == 0){
                $this -> error('套餐ID有错误');
            }
            $model = M( 'SecondTheme','','DB_CONFIG2' );
            $info =  $model->where( array('id' => $themeId ))->field('goods,instrument')->find();
            $goodsList = array();
            $goods = json_decode($info['goods'],true);
            foreach ($goods as $k => $v){
                $goodsId = intval($v['goodsId']);
                $where['id'] = $goodsId;
                $goodsInfo = M('goods','','DB_CONFIG2')->where($where)->field('id,first_img,price,name')->find();
                $goodsInfo['sale_price'] = $v['salePrice'];
                $goodsList[] = $goodsInfo;
            }
            $this -> ajaxReturn( $goodsList );
        }
    }



    /**
     * 主题采购相关商品和仪器列表
     */
    public function instrumentList() {
        if(IS_POST){
            $themeId = intval($_POST['theme_id']);
            if($themeId == 0){
                $this -> error('套餐ID有错误');
            }
            $model = M( 'SecondTheme','','DB_CONFIG2' );
            $info =  $model->where( array('id' => $themeId ))->field('goods,instrument')->find();
            $instrumentList = array();
            $instrument = json_decode($info['instrument'],true);
            foreach ($instrument as $k => $v){
                $goodsId = intval($v['goodsId']);
                $where['id'] = $goodsId;
                $goodsInfo = M('goods','','DB_CONFIG2')->where($where)->field('id,first_img,price,name')->find();
                $goodsInfo['sale_price'] = $v['salePrice'];
                $instrumentList[] = $goodsInfo;
            }

            $this -> ajaxReturn( $instrumentList );
        }
    }



    //主题采购列表
    public function secondThemeList(){
        //商品的一级分类
        $goodsCategoryList = M('goods_category','','DB_CONFIG2') -> where(array('parent_id' => 0)) -> field('id,name') -> select();
        $this -> goodsCategoryList = $goodsCategoryList;
        $this ->display();
    }

    public function ajaxSecondThemeList(){
        //套餐列表
        // where条件
        $where = array ();
        if (intval($_GET['category_id'])) {
            $where['category_id'] = array(intval($_GET['category_id']));
        }
        if (trim($_GET['keyword']) != '') {
            $where['name'] = array('like', '%' . trim($_GET['keyword']) . '%');
        }

        $model = M('secondTheme','','DB_CONFIG2'); // 实例化User对象
        $count = $model->where($where)->count();    //计算总数
        $page = new \Component\PageAjax($count, 2);
        $secondThemeList = $model->where($where)->limit($page->firstRow . ',' . $page->listRows)->order('id DESC')->select();
        $pageList =$page->show();
        $this -> secondThemeList = $secondThemeList;
        $this -> pageList     = $pageList;
        $this ->display();

    }


    //删除主题采购二级信息
    public function delSecondTheme(){
        if(IS_POST) {
            $model = M('SecondTheme', '', 'DB_CONFIG2');
            if (isset($_POST['theme_id']) && intval($_POST['theme_id'])) {//删除单条数据
                $where['id'] = intval($_POST['theme_id']);
            }

            if (isset($_POST['theme_ids']) && !empty($_POST['theme_ids'])) {
                $where['id'] = array('in', $_POST['theme_ids']);
            }

            $result = $model->where($where)->delete();
            if ($result) {
                $this->success('删除成功');
            } else {
                $this->error('删除失败');
            }
        }
    }

}
