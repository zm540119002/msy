<?php
namespace Admin\Controller;

use web\all\Controller\BaseController;

class CityController extends BaseController {
    /**城市-管理
     */
    public function cityManage(){
        $modelCity = D('City');
        if(IS_POST){
            $this->cityList = $modelCity->selectCity();
            $this->display('cityList');
        }else{
            $this->cityList = $modelCity->selectCity();
            $this->display();
        }
    }

    /**城市-编辑
     */
    public function cityEdit(){
        $modelCity = D('City');
        if(IS_POST){
            if(isset($_POST['cityId']) && intval($_POST['cityId'])){
                $res = $modelCity->saveCity();
            }else{
                $res = $modelCity->addCity();
            }
            $this->ajaxReturn($res);
        }else{
            //省份
            $modelProvince = D('Province');
            $this->provinceList = $modelProvince->selectProvince();
            //城市级别
            $this->cityType = C('CITY_TYPE');
            if (isset($_GET['cityId']) && intval($_GET['cityId'])){
                $cityId = I('get.cityId', 0, 'int');
                $where = array(
                    'ct.id' => $cityId,
                );
                $cityInfo = $modelCity->selectCity($where);
                $this->cityInfo = $cityInfo[0];
            }
            $this->display();
        }
    }

    //城市列表
    public function cityList(){
        if(!IS_GET){
            $this->ajaxReturn(errorMsg(C('NOT_GET')));
        }
        $modelCity = D('City');
        $where = array(
            'ct.status' => 0,
        );
        $keyword = I('get.keyword','','string');
        if($keyword){
            $where['_complex'] = array(
                'ct.value' => array('like', '%' . trim($keyword) . '%'),
            );
        }
        $field = array(
        );
        $join = array(
        );
        $order = 'ct.province_id,ct.id';
        $group = "";
        $pageSize = (isset($_GET['pageSize']) && intval($_GET['pageSize'])) ? I('get.pageSize',0,'int') : C('DEFAULT_PAGE_SIZE');
        $cityList = page_query($modelCity,$where,$field,$order,$join,$group,$pageSize,$alias='ct');
        $this->cityList = $cityList['data'];
        $this->pageList = $cityList['pageList'];
        //所有省份
        $modelProvince = D('Province');
        $this->provinceList = $modelProvince->selectProvince();
        $this->display();
    }

    /**城市-删除
     */
    public function delCity(){
        if(!IS_POST){
            $this->ajaxReturn(errorMsg(C('NOT_POST')));
        }
        $modelCity = D('City');
        $res = $modelCity->delCity();
        $this->ajaxReturn($res);
    }
}