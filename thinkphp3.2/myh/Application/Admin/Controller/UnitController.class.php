<?php
namespace Admin\Controller;

use web\all\Controller\BaseController;

class UnitController extends BaseController {
    /**单位-管理
     */
    public function unitManage(){
<<<<<<< HEAD
        $modelUnit = D('Mall/Unit');
=======
        $modelUnit = D('Business/Unit');
>>>>>>> 918034240c5e655e5d543e81e3d131f7c9e53042
        if(IS_POST){
            $this->unitList = $modelUnit->selectUnit();
            $this->display('unitList');
        }else{
            $this->unitList = $modelUnit->selectUnit();
            $this->display();
        }
    }

    /**单位-编辑
     */
    public function unitEdit(){
<<<<<<< HEAD
        $modelUnit = D('Mall/Unit');
=======
        $modelUnit = D('Business/Unit');
>>>>>>> 918034240c5e655e5d543e81e3d131f7c9e53042
        if(IS_POST){
            if(isset($_POST['unitId']) && intval($_POST['unitId'])){
                $res = $modelUnit->saveUnit();
            }else{
                $res = $modelUnit->addUnit();
            }
            $this->ajaxReturn($res);
        }else{
            if (isset($_GET['unitId']) && intval($_GET['unitId'])){
                $unitId = I('get.unitId', 0, 'int');
                $where = array(
                    'ut.id' => $unitId,
                );
                $unitInfo = $modelUnit->selectUnit($where);
                $this->unitInfo = $unitInfo[0];
            }
            $this->display();
        }
    }

    //单位列表
    public function unitList(){
        if(!IS_GET){
            $this->ajaxReturn(errorMsg(C('NOT_GET')));
        }

<<<<<<< HEAD
        $modelUnit = D('Mall/Unit');
=======
        $modelUnit = D('Business/Unit');
>>>>>>> 918034240c5e655e5d543e81e3d131f7c9e53042
        $where = array(
            'ut.status' => 0,
        );
        $keyword = I('get.keyword','','string');
        if($keyword){
            $where['_complex'] = array(
                'ut.value' => array('like', '%' . trim($keyword) . '%'),
            );
        }
        $field = array(
        );
        $join = array(
        );

        $order = 'ut.key,ut.id';
        $group = "";
        $pageSize = (isset($_GET['pageSize']) && intval($_GET['pageSize'])) ? I('get.pageSize',0,'int') : C('DEFAULT_PAGE_SIZE');

        $unitList = page_query($modelUnit,$where,$field,$order,$join,$group,$pageSize,$alias='ut');

        $this->unitList = $unitList['data'];
        $this->pageList = $unitList['pageList'];
        $this->display();
    }

    /**单位-删除
     */
    public function delUnit(){
        if(!IS_POST){
            $this->ajaxReturn(errorMsg(C('NOT_POST')));
        }
<<<<<<< HEAD
        $modelUnit = D('Mall/Unit');
=======
        $modelUnit = D('Business/Unit');
>>>>>>> 918034240c5e655e5d543e81e3d131f7c9e53042
        $res = $modelUnit->delUnit();
        $this->ajaxReturn($res);
    }
}