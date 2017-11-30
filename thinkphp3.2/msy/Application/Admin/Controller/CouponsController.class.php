<?php
namespace Admin\Controller;

use web\all\Controller\BaseController;

class CouponsController extends BaseController {
    /**代金券分类-管理
     */
    public function couponsManage(){
        $model = D('Coupons');
        if(IS_POST){
            $this->couponsList = $model->selectCoupons();
            $this->display('couponsList');
        }else{
            $this->couponsList = $model->selectCoupons();
            $this->display();
        }
    }

    /**代金券分类-编辑
     */
    public function couponsEdit(){
        $model = D('Coupons');
        if(IS_POST){
            //日期转时间戳
            if( isset($_POST['failure_time']) && $_POST['failure_time']){
                $_POST['failure_time'] = strtotime($_POST['failure_time']);
            }
            if(isset($_POST['couponsId']) && intval($_POST['couponsId'])){
                $_POST['update_time'] = time();
                $res = $model->saveCoupons();
            }else{
                $_POST['create_time'] = time();
                $res = $model->addCoupons();
            }
            $this->ajaxReturn($res);
        }else{
            if (isset($_GET['couponsId']) && intval($_GET['couponsId'])){
                $couponsId = I('get.couponsId', 0, 'int');
                $where = array(
                    'c.id' => $couponsId,
                );
                $couponsInfo = $model->selectCoupons($where);
                $couponsInfo = $couponsInfo[0];
                //格式化失效时间
                if($couponsInfo['failure_time']){
                    $couponsInfo['failure_time'] = date('Y-m-d',$couponsInfo['failure_time']);
                }
                $this->couponsInfo = $couponsInfo;
            }
            $this->couponsTypeList = C('ARR')['coupons_type'];
            $this->couponsSceneList = C('ARR')['coupons_scene'];
            $this->display();
        }
    }

    //代金券列表
    public function couponsList(){
        if(!IS_GET){
            $this->ajaxReturn(errorMsg(C('NOT_GET')));
        }

        $model = D('Coupons');
        $where = array(
            'c.status' => 0,
        );
        $keyword = I('get.keyword','','string');
        if($keyword){
            $where['_complex'] = array(
                'c.name' => array('like', '%' . trim($keyword) . '%'),
            );
        }
        $field = array(
        );
        $join = array(
        );

        $order = 'c.id';
        $group = "";
        $pageSize = (isset($_GET['pageSize']) && intval($_GET['pageSize'])) ? I('get.pageSize',0,'int') : C('DEFAULT_PAGE_SIZE');

        $couponsList = page_query($model,$where,$field,$order,$join,$group,$pageSize,$alias='c');

        $this->couponsList = $couponsList['data'];
        $this->pageList = $couponsList['pageList'];
        $this->display();
    }

    /**代金券分类-删除
     */
    public function delCoupons(){
        if(!IS_POST){
            $this->ajaxReturn(errorMsg(C('NOT_POST')));
        }
        $model = D('Coupons');
        $res = $model->delCoupons();
        $this->ajaxReturn($res);
    }
}