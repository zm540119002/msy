<?php
namespace Admin\Controller;
use Think\Controller;
use Common\Controller\BaseController;
class PurchaseThemeController extends BaseController {
    public function index(){
        $this->display();
    }

    /**
     * 主题采购一级列表
     */
    public function themePurchaseList1(){
        //商品的一级分类
        $goodsCategoryList = M('goods_category','','DB_CONFIG2') -> where(array('parent_id' => 0)) -> field('id,name') -> select();
        $this -> goodsCategoryList = $goodsCategoryList;
        $this ->display();
    }

    public function ajaxThemeList1(){
        //套餐列表
        // where条件
        $where = array ();
        if (intval($_GET['category_id']) > 0) {
            $where['category_id'] = array(intval($_GET['category_id']));
        }
        if (trim($_GET['keyword']) != '') {
            $where['name'] = array('like', '%' . trim($_GET['keyword']) . '%');
        }

        $model = M('FirstTheme','','DB_CONFIG2'); // 实例化User对象
        $count = $model->where($where)->count();    //计算总数
        $page = new \Component\PageAjax($count, 2);
        $firstThemeList = $model->where($where)->limit($page->firstRow . ',' . $page->listRows)->order('id DESC')->select();
        $pageList =$page->show();
        $this -> firstThemeList = $firstThemeList;
        $this -> pageList     = $pageList;
        $this ->display();

    }



    //添加修改主题采购页面展示
    public function addThemePurchase1(){
        if( isset($_GET['theme_id'] ) && ( $_GET['theme_id']) ){
            $theme_id = intval($_GET['theme_id']);
            if($theme_id == 0){
                $this -> error('参数错误');
            }
            $condition = array('id' => $theme_id);
            $themePurchaseList = $this -> getThemePurchaseList1($condition);
            $this -> themePurchaseList = $themePurchaseList;
        }
        //商品的一级分类
        $goodsCategoryList = M('goods_category','','DB_CONFIG2') -> where(array('parent_id' => 0)) -> field('id,name') -> select();
        $this -> goodsCategoryList = $goodsCategoryList;
        $this -> display();
    }


    /**
     * 添加修改一级主题采购
     */
    public function addEditThemePurchase1(){
        if(IS_POST){
            $rules = array(
                array('name','require','主题名称必须！'),
                array('img','require','请上传图片'),
                array('category_id','require','请选择分类！'),
                array('intro','require','请填写简介！'),

            );
            $model = M('FirstTheme','','DB_CONFIG2');
            $res = $model->validate($rules)->create();
            if(!$res){
                $this->error($model->getError());
            }

            $newRelativePath = C('THEME_IMG_PATH');
            if( isset($_POST['img'] ) && $_POST['img'] ){
                $_POST['img'] = moveImgFromTemp($newRelativePath,basename( $_POST['img']));
            }

            if( isset($_POST['theme_id']) && intval($_POST['theme_id']) ){//修改
                $where['id'] = intval($_POST['theme_id']);
                $result = $model->where($where) ->save($_POST);
                if(false===$result){
                    $this->error('修改失败');
                }else{
                    $this->success('修改成功');
                }
            }else{//增加
               $result = $model ->add($_POST);
                if($result){
                    $this->success('添加成功');
                }else{
                    $this->error('添加失败');
                }
            }
        }

    }

    //根据id获取主题采购一级信息
    public function getThemePurchaseList1($condition){
        $model = M('FirstTheme','','DB_CONFIG2');
        $result = $model -> where($condition)  -> find();
        return $result;
    }
    
    
    //删除主题采购一级信息
    public function delFirstTheme(){
        if(IS_POST) {
            $model = M('FirstTheme', '', 'DB_CONFIG2');
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
