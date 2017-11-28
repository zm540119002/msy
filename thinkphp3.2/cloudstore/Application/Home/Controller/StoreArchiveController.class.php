<?php
namespace Home\Controller;

use web\all\Cache\CompanyCache;

//店家档案
class StoreArchiveController extends BaseAuthCompanyController {
    //编辑美容机构基本资料
    public function editOrganization(){
        if(IS_POST){
            $rules = array(
                array('name','require','美容机构全称必须！'),
                array('shorten_name','require','美容机构简称必须！'),
                array('intro','require','美容机构简称必须！'),
            );
            if( isset($_POST['companyId']) && intval($_POST['companyId']) ){
                $this->saveCompany($rules);
            }else{
                $this->addCompany($rules);
            }
        }else{
            $this->assign('company',$this->company);

            $this->display();
        }
    }

    //预览美容机构的基本资料
    public function previewOrganization(){
        $companyInfo = $this->company;
        //判断是否都为空
        foreach ($companyInfo as $key => $val){
            if($val != 'logo' || $val != 'name' || $val != 'shorten_name' || $val != 'intro'){
                if($val){
                    $this->figureSign = 'true';
                }
            }
        }

        $this->assign('companyInfo',$companyInfo);

        $this->display();
    }

    //预览美容机构的店家档案
    public function previewOrganizationArchive(){
        $this->assign('company',$this->company);
        //判断是否都为空
        foreach ($this->company as $key => $val){
            if($val != 'logo' || $val != 'name' || $val != 'shorten_name' || $val != 'intro'){
                if($val){
                    $this->figureSign = 'true';
                }
            }
        }

        $where = array(
            's.user_id' => $this->user['id'],
            's.company_id' => $this->company['id'],
        );
        $this->shopList = $this->selectShop($where);

        $this->display();
    }

    //企业组织架构和岗位模型选择
    public function scaleType(){
        if(IS_POST){
            //原来的组织架构
            $scaleTypeOriginal = $this->company['scale'];
            //要设置的组织架构
            $scaleTypeSet = I('post.scaleType',0,'int');

            if($scaleTypeOriginal == 0 || $scaleTypeOriginal == 1 || $scaleTypeOriginal == 2){//组织架构未初始化或原来为单店的情况
            }else{//组织架构已初始化
                if($scaleTypeOriginal == 3){//原来为中小型连锁店的情况
                }elseif($scaleTypeOriginal == 4){//原来为大型连锁店的情况
                }
            }

            $this->_setCompanyScaleType($scaleTypeSet);
        }else{
            $this->scaleType = $this->company['scale'];

            if($this->scaleType == 0){//组织架构未初始化
                $this->display();
            }else{//组织架构已初始化
                $elseScale = C('SCALE');
                foreach ($elseScale as $key => $val){
                    if($key != $this->scaleType){
                        $arr[] = $key;
                    }
                }
                $this->list = $arr ;
                $this->display('scaleType_2');
            }
        }
    }

    //企业组织架构和岗位模型说明
    public function positionIllustrate(){
        if(IS_POST){
        }else{
            $scaleType = I('get.scaleType',0,'int');
            $scale = C('SCALE');
            $this->scale = $scale[$scaleType];
            $this->scaleType = $scaleType;
            $this->companyId = $this->company['id'];

            $this->display();
        }
    }

    //百度地图定位
    public function baiduMap(){
        if(IS_POST){
            if(isset($_POST['shopId']) && intval($_POST['shopId'])){
                $_POST['id'] = I('post.shopId',0,'int');
                $_POST['identified'] = 1;
                $shop = $this->_saveShop();
                if($shop){
                    $this->ajaxReturn(successMsg('成功'));
                }else{
                    $this->ajaxReturn(errorMsg('失败'));
                }
            }
        }else{
            $addr = I('get.addr','','string');
            $this->assign('addr',$addr);

            $shopId = I('get.shopId',0,'int');
            $this->assign('shopId',$shopId);

            $this->display();
        }
    }

    //单店
    public function singleShop(){
        if(IS_POST){
            $newRelativePath = C('SHOP_LOGO');
            if( isset($_POST['logo']) && $_POST['logo'] ){
                $_POST['logo'] = $this->moveImgFromTemp($newRelativePath,basename($_POST['logo']));
            }
            if(isset($_POST['shopId']) && $_POST['shopId'] ){//修改
                //过滤ID
                $_POST['shopId'] = intval($_POST['shopId']);
                $shop = $this->_saveShop();
            }else{//新增
                $shop = $this->_addShop();
            }
            if($shop){
                $this->ajaxReturn(successMsg('成功'));
            }else{
                $this->ajaxReturn(errorMsg('失败'));
            }
        }else{
            $where = array(
                's.company_id' => $this->company['id'],
                'c.type' => 0,
            );
            $shop = $this->selectShop($where);
            $this->assign('shop',$shop[0]);
            $this->display();
        }
    }

    //小型连锁店
    public function editShopList(){
        if(IS_POST){
            $newRelativePath = C('SHOP_LOGO');
            if( isset($_POST['logo']) && $_POST['logo'] ){
                $_POST['logo'] = $this->moveImgFromTemp($newRelativePath,basename($_POST['logo']));
            }
            if(isset($_POST['shopId']) && $_POST['shopId'] ){//修改
                //过滤ID
                $_POST['shopId'] = intval($_POST['shopId']);
                $shop = $this->_saveShop();
            }else{//新增
                $shop = $this->_addShop();
            }
            $this->assign('shop',$shop);
            $this->display('Public/shopInfo');
        }else{
            $where = array(
                's.user_id' => $this->user['id'],
            );
            if(isset($_GET['companyId']) && $_GET['companyId']){
                $companyId = I('get.companyId',0,'int');
                $where['s.company_id'] = $companyId;
                $this->assign('companyId',$companyId);
            }
            if(isset($_GET['companyName']) && $_GET['companyName']){
                $companyName = I('get.companyName','','string');
                $this->assign('companyName',$companyName);
            }
            $this->shopList = $this->selectShop($where);

            $this->display();
        }
    }

    //大型连锁店
    public function editCompanyList(){
        if(IS_POST){
            if(isset($_POST['companyId']) && $_POST['companyId'] ){//修改
                //过滤ID
                $_POST['companyId'] = intval($_POST['companyId']);
                $this->saveCompany();
            }else{//新增
                $_POST['type'] = 1;
                $_POST['father_id'] = $this->company['id'];
                $this->addCompany();
            }
        }else{
            $where = array(
                'c.user_id' => $this->user['id'],
            );
            $this->companyList = $this->selectCompany($where);

            $this->display();
        }
    }

    //新增门店
    private function _addShop(){
        if(!IS_POST){
            $this->ajaxReturn(errorMsg('请用POST访问'));
        }
        $model = M('shop');
        $rules = array(
            array('name','require','门店名称必须！'),
            array('name','','门店名称已经存在！',0,'unique',1),
            array('receptionist_mobile','require','手机号码必须！'),
            array('receptionist_mobile','','手机号码已经存在！',0,'unique',1),
        );
        $_POST['user_id'] = $this->user['id'];
        $_POST['company_id'] = $_POST['companyId']?intval($_POST['companyId']):$this->company['id'];
        $_POST['type'] = 1;
        $_POST['create_time'] = time();
        $res = $model->validate($rules)->create();
        if(!$res){
            $this->ajaxReturn(errorMsg($model->getError()));
        }

        $shopId = $model->add();
        if(!$shopId){
            $this->ajaxReturn(errorMsg($model->getError()));
        }

        $_POST['id'] = $shopId;
        return $_POST;
    }

    //修改门店
    private function _saveShop(){
        if(!IS_POST){
            $this->ajaxReturn(errorMsg('请用POST访问'));
        }
        if(!isset($_POST['shopId']) || !$_POST['shopId']){
            $this->ajaxReturn(errorMsg('缺少门店ID参数！'));
        }

        $id = I('post.shopId',0,'number_int');

        $model = M('shop');
        $rules = array(
            array('name','require','门店名称必须！'),
            array('receptionist_mobile','require','手机号码必须！'),
        );
        if(isset($_POST['companyId']) && intval($_POST['companyId'])){
            $_POST['company_id'] = I('post.companyId',0,'int');
        }
        $_POST['update_time'] = time();
        $res = $model->validate($rules)->create();
        if(!$res){
            $this->ajaxReturn(errorMsg($model->getError()));
        }

        $where = array(
            'id' => $id,
            'user_id' => $this->user['id'],
            'status' => 0,
        );
        $res = $model->where($where)->save();
        if(false === $res){
            $this->ajaxReturn(errorMsg($model->getError()));
        }

        $_POST['id'] = $id;
        return $_POST;
    }

    //删除门店
    public function delShop(){
        if(!IS_POST){
            $this->ajaxReturn(errorMsg('请用POST访问'));
        }
        if(!isset($_POST['shopId']) || !$_POST['shopId']){
            $this->ajaxReturn(errorMsg('缺少门店ID参数！'));
        }
        $id = I('post.shopId',0,'int');
        $where = array(
            'id' => $id,
            'user' => $this->user['id'],
            'company_id' => $_POST['companyId']?intval($_POST['companyId']):$this->company['id'],
        );
        $model = M('shop');
        $res = $model->where($where)->setField('status',2);
        if($res === false){
            $this->ajaxReturn(errorMsg($model->getError()));
        }

        $this->ajaxReturn(successMsg('删除成功'));
    }

    //删除公司
    public function delCompany(){
        if(!IS_POST){
            $this->ajaxReturn(errorMsg('请用POST访问'));
        }
        if(!isset($_POST['companyId']) || !$_POST['companyId']){
            $this->ajaxReturn(errorMsg('缺少公司ID参数！'));
        }
        if(!isset($_POST['companyType'])){
            $this->ajaxReturn(errorMsg('缺少公司类型参数！'));
        }

        $type = I('post.companyType',0,'number_int');
        if($type == 0){
            $this->ajaxReturn(errorMsg('总公司不能删除！'));
        }
        $id = I('post.companyId',0,'number_int');
        $where = array(
            'id' => $id,
            'founder_id' => $this->user['id'],
            'store_id' => $this->user['id'],
        );
        $model = M('company');
        $res = $model->where($where)->setField('status',2);
        if(!$res){
            $this->ajaxReturn(errorMsg($model->getError()));
        }

        $this->ajaxReturn(successMsg('删除成功'));
    }

    //设置企业组织架构
    private function _setCompanyScaleType($scaleTypeSet){
        if(!IS_POST){
            $this->ajaxReturn(errorMsg('请用POST访问！'));
        }

        $model = M('company');
        $where = array(
            'id' => $this->company['id'],
        );
        $res = $model->where($where)->setField('scale',$scaleTypeSet);
        if(false === $res){
            $this->ajaxReturn(errorMsg($model->getError()));
        }
        CompanyCache::remove($this->user['id']);
        $this->ajaxReturn(successMsg('设置成功'));
    }
}