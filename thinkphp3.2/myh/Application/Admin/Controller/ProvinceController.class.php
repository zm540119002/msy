<?php
namespace Admin\Controller;

use web\all\Controller\BaseController;

class ProvinceController extends BaseController {
    /**省份-管理
     */
    public function provinceManage(){
        $modelProvince = D('Business/Province');
        if(IS_POST){
            $this->provinceList = $modelProvince->selectProvince();
            $this->display('provinceList');
        }else{
            $this->provinceList = $modelProvince->selectProvince();
            $this->display();
        }
    }

    /**省份-编辑
     */
    public function provinceEdit(){
        $modelProvince = D('Business/Province');
        if(IS_POST){
            if(isset($_POST['provinceId']) && intval($_POST['provinceId'])){
                $res = $modelProvince->saveProvince();
            }else{
                $res = $modelProvince->addProvince();
            }
            $this->ajaxReturn($res);
        }else{
            if (isset($_GET['provinceId']) && intval($_GET['provinceId'])){
                $provinceId = I('get.provinceId', 0, 'int');
                $where = array(
                    'pv.id' => $provinceId,
                );
                $provinceInfo = $modelProvince->selectProvince($where);
                $this->provinceInfo = $provinceInfo[0];
            }
            $this->display();
        }
    }

    //省份列表
    public function provinceList(){
        if(!IS_GET){
            $this->ajaxReturn(errorMsg(C('NOT_GET')));
        }
        $modelProvince = D('Business/Province');
        $where = array(
            'pv.status' => 0,
        );
        $keyword = I('get.keyword','','string');
        if($keyword){
            $where['_complex'] = array(
                'pv.name' => array('like', '%' . trim($keyword) . '%'),
            );
        }
        $field = array(
        );
        $join = array(
        );
        $order = 'pv.id';
        $group = "";
        $pageSize = (isset($_GET['pageSize']) && intval($_GET['pageSize'])) ? I('get.pageSize',0,'int') : C('DEFAULT_PAGE_SIZE');
        $provinceList = page_query($modelProvince,$where,$field,$order,$join,$group,$pageSize,$alias='pv');
        $this->provinceList = $provinceList['data'];
        $this->pageList = $provinceList['pageList'];
        $this->display();
    }

    /**省份-删除
     */
    public function delProvince(){
        if(!IS_POST){
            $this->ajaxReturn(errorMsg(C('NOT_POST')));
        }
        $modelProvince = D('Business/Province');
        $res = $modelProvince->delProvince();
        $this->ajaxReturn($res);
    }
}