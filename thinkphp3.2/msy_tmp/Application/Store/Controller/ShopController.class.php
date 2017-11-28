<?php
namespace Store\Controller;

use SellerCompany\Controller\BaseAuthCloudstoreShopController;
use Store\Model\CompanyModel;

class ShopController extends BaseAuthCloudstoreShopController {
    public function index(){
        
        
      $this -> display();

    }
  //门店设置页面
   public function SetStore(){
       if(IS_POST){

       }else{
           $where = array(
               'uid' => session('uid'),
           );
           //获取机构类型的状态
           $shopType = M('company') -> where($where) ->getField('shop_type');
           $this -> shopType = intval($shopType);
           $this -> display();
       }

   }

    /**
     * 机构基本信息编辑页面
     */
    public function base_info(){
        if(IS_POST){

        }else{
            $where = array(
                'uid' => session('uid'),
            );
            $info = M('company') -> where($where) ->find();
            $this -> info = $info;
            $this -> display();
        }

    }

    /**
     * 上传机构图片并按照路径保存
     * @return string 图片的地址
     */
    public function ajaxupload(){
        $base64_image = $_POST['img'];
        $field = $_POST['field'];
        $where = array(
            'uid' => session('uid'),
        );
        //如果有图片先删除再上传
        $info = M('company') ->where($where) ->getField($field);
        if (file_exists($info)) {
            if (!unlink($info)) {
                show(0,"删除原来存在图失败");
            }
        }

        //图片上传路径
        $UPLOAD_PATH = C('UPLOAD_PATH');
        $COMPANY_FIGURE_PATH = C('COMPANY_FIGURE_PATH');
        $path = $UPLOAD_PATH.$COMPANY_FIGURE_PATH;

        $imgUrl = upload($base64_image,$path);
        if($imgUrl == ''){
            show(0,"上传图片失败");
        }

        $image = new \Think\Image();
        //open();打开图像资源，通过路径名找到图像
        $image -> open($imgUrl);
        if($field == 'imglogourl'){  //logo图片上传
            $image -> thumb(1000,750,true) ->save($imgUrl);  //按照比例缩小
        }else{
            $image -> thumb(800,600,true) ->save($imgUrl);
        }
        
        //把图片路径插入数据库
        if(!empty($imgUrl)) {
            $uid = session('uid');
           $ret =  M('company')->execute(" update company set $field= '$imgUrl' where uid=$uid");
           if(!$ret){
                show(0,"上传图片失败");
           }else{
               show(1,"上传图片成功",$imgUrl);
           }
        }

    }

    /**
     *  机构基本信息提交
     */
    public function editInfo()
    {
        if(IS_POST){
            $data =I('post.');
            $where = array(
                'uid' => session('uid'),
            );
            if($data['name'] == ''){
                show(0,'请填写美容机构全称');
            }
            if($data['organizajc'] == ''){
                show(0,'请填写美容机构简称');
            }
            if($data['description'] == ''){
                show(0,'请填写美容机构简介');
            }
            $info= M('company')->where($where)->field('name,organizajc,description')->select();
            if($info[0] == $data){
                show(1,'美容机构全称、简称和简介数据没有更改');
            }
            
            $rst = M('company') -> where($where) -> save($data);

            if($rst){
                show(1,'编辑完成，可在预览栏查看资料',$rst);
            }else{
                show(0,'提交数据失败');
            }
        }

    }

    /**
     * 预览美容机构基本资料
     */

    public function prestore_base(){
        $companyInfo = D('Company') -> getCompanyInfo();
        $this -> companyInfo = $companyInfo;
        $this -> display();
    }



    //企业组织架构和岗位模型选择
    public function shopType(){
        if(IS_POST){
            $shopType = I('post.');
            show(1,'',$shopType);
        }

        $this -> display();
    }

    //
    public function shopType_2(){
        if(IS_POST){

        }else{
            $shopType = I('get.shopType','','int');
            if($shopType < 0 || $shopType > 5){
                $this->error('机构类型参数有误');
            }
            $list = array('1'=>1,'2'=>2,'3'=>3,'4'=>4);
            foreach ($list as $key=>$value){
                if($value == $shopType){
                    unset($list[$key]);
                }
            }
            $this ->shopType = $shopType;
            $this ->list = $list;
            $this -> display();
        }

    }

    /**
     *  企业组织架构和岗位模型说明
     */
    public function shopFramework(){
        $shopType = I('get.shopType','','int');
        if($shopType > 0 && $shopType < 5){
            $scale = C('SCALE');
            $this -> scaleInfo = $scale[$shopType];
            $this ->shopType = $shopType;
            $this -> display();
        }else{
            $this->error('机构类型参数有误');
        }

    }
   //单店编辑门店地址
    public function smallShop(){
        if(IS_POST){
            $this -> editShopAddress();
        }else{
            $shopType = I('get.shopType','','int');
            if($shopType < 0 || $shopType > 5){
                show(0,'机构的类型参数有误');
            }
            $where = array('uid' => session('uid'));
            $shopInfo = M('shop') -> where($where) -> find();
            $this ->shopType = $shopType;
            $this ->shopInfo = $shopInfo;
            $this -> display();
        }
    }

    /**
     *  美容机构类型为1:单店 不设部门  2:单店 设部门  编辑门店地址
     */
    public function editShopAddress(){

        if(IS_POST){
            $model_shop = D('shop');
            $data = I('post.');
            $data['uid'] = session('uid');

            $shopType = intval($data['shop_type']);
            if($shopType < 0 || $shopType > 5){
                show(0,'机构的类型参数有误');
            }
            if (!$model_shop->create()) {
                $errorInfo = $model_shop->getError();
                show(0, $errorInfo);
            }

            $where = array('uid' => session('uid'),);

            $shopType = M('company') -> where($where) -> getField('shop_type');
            if(intval($shopType) == 1 || intval($shopType) == 2 ){ //shop_type = 1 || 2 只是修改门店地址
                $data['update_time'] = time();
                $shop_id = $model_shop -> where($where)-> save($data);
                if(!$shop_id){
                    show(0,'编辑门店地址失败');
                }else{
                    show(1,'编辑门店地址成功');
                }
            }else{//增加门店
                $model_shop->startTrans(); //开启事务
                if(intval($shopType)!= intval($data['shop_type']) || intval($shopType) == 0){
                    // 更改机构状态,如果shop_type = 0或不等于现在所选的类型就更改，否则就不更改
                    $data2 = array('shop_type' => $data['shop_type']);
                    $shopType = M('company') -> where($where) -> save($data2);
                    if(!$shopType){
                        $model_shop->rollback();//回滚
                        show(0,'更改机构状态不成功');
                    }
                }

                $data['company_id'] = intval(D('company')->getCompanyID());
                $data['create_time'] = time();
                $shop_id = $model_shop -> add($data);
                if(!$shop_id){
                    $model_shop->rollback();//回滚
                    show(0,'编辑门店地址失败');
                }
                    $model_shop->commit();//提交
                    show(1,'编辑门店地址成功',$shop_id);
            }
        }
    }



    //机构 连锁店 不设分公司 
    public function smallMultipleShop(){
        if(IS_POST){
            if(isset($_POST['shopId']) && $_POST['shopId'] ){//修改
                //过滤ID
                $_POST['shopId'] = intval($_POST['shopId']);
                $this->saveShop();
            }else if(isset($_POST['img']) && $_POST['img']){
                $this -> uploadShopLogo();
            }else{//新增
                $this->addShop();
            }

        }else{
            $where = array(
                'uid' => session('uid'),
                'status'=> 0, //正常状态 1删除
            );
            $shopList = M('shop') -> where($where)  ->select();
            $this -> shopList = $shopList;
            $this->display();
        }
    }

    /**
     * 大型有分公司机构
     */
    public function bigmultipleshop(){
        if(IS_POST){
            if(isset($_POST['companyId']) && $_POST['companyId'] ){//修改
                //过滤ID
                $_POST['companyId'] = intval($_POST['companyId']);
                $this->saveCompany();
            }else{//新增
                $this->addCompany();
            }

        }else{
            $companyList = D('Company') -> getCompany();
            $this -> companyList = $companyList;
            $this->display();
        }
    }


    /**
     * 增加分公司
     */
    private function addCompany(){
       $name = I('post.name');
       if(!$name){
           show(0,'请填分公司名');
       }
        $where = array('uid' => session('uid'),);
        $shopType = M('company') -> where($where) -> getField('shop_type');
        if(intval($shopType) != 4  ){
                $data = array('shop_type' => 4);//shop_type = 4 美容机构类型为连锁店 设分公司,
                $shopType = M('company') -> where($where) -> save($data);
                if(!$shopType){
                    show(0,'更改机构状态不成功');
                }
        }
       $data = array(
           'name' => $name,
           'pid'  => intval(D('company')->getCompanyID()),
           'type' => 1,
           'create_time' => time(),
       );
       $company_id = M('company') -> add($data);
       if($company_id){
           show(1,'增加分公司成功',$company_id);
       }else{
           show(0,'增加分公司失败');
       }

   }
    /**
     * 修改分公司名字
     */
    private function saveCompany(){
        if(IS_POST){
            if(!D('company')->create()){
                $info = D('company')->getError();
                show(0, $info);
            }
            $where = array('id' => I('post.companyId',0,'number_int'));
            $data = array(
                'name' => I('post.name'),
                'update_time' => time(),
            );
            $res = D('company')->where($where)->save($data);
            if(false === $res){
               show(0,(D('company')->getError()));

            }

        }

    }

    /**
     * 删除分公司
     */
    public function delCompany(){
        
    }

    /**
     * 大型有分公司机构,编辑门店
     */
    public function BranchShopAddress(){
        if(IS_POST){

            if(isset($_POST['shopId']) && $_POST['shopId'] ){//修改
                //过滤ID
                $_POST['shopId'] = intval($_POST['shopId']);
                $this->saveShop();
            }else if(isset($_POST['img']) && $_POST['img']){
                $this -> uploadShopLogo();
            }else{//新增
                $this->addShop();
            }

        }else{
            $companyList = D('Company') -> getCompany();
            $this -> companyList = $companyList;
            $where = array(
                'uid' => session('uid'),
                'status'=> 0, //正常状态 1删除
            );
            $shopList = M('shop') -> where($where)  ->select();
            $this -> shopList = $shopList;
            $this->display();
        }
    }
    
    
    
    /**
     *  连锁店 设分公司类型的美容机构增加门店
     */
    public function addShop(){
        if(IS_POST){
            if(!D('shop')->create()){
                $error_info = D('shop')->getError();
                show(0, $error_info);
            }

            $temp_path =I('post.logo_url');
            $arr=explode('/',$temp_path);
            $imgName = $arr[2];
            $SHOP_LOGO = C('SHOP_LOGO');
            if(!file_exists($SHOP_LOGO)){
                //检查是否有该文件夹，如果没有就创建，并给予最高权限
                if(!mkdir($SHOP_LOGO, 0700,true)){
                    show(0,'创建目录失败');
                }
            }

            if(!rename($temp_path, $SHOP_LOGO.$imgName)){
                show(0,'图片移动失败');
            }
           if(I('post.company_id',0,'number_int')==''){//如果没有分公司ID，找机构ID
               $company_id = intval(D('company')->getCompanyID());
           }else{
               $company_id = I('post.company_id',0,'number_int');
           }
            $data = array(
                'company_id'=> $company_id,
                'company_name'=> I('post.company_name'),
                'storename'=> I('post.storename'),
                'address'=> I('post.address'),
                'fiex_mobile'=> I('post.fiex_mobile',0,'number_int'),
                'mobile'=> I('post.mobile',0,'number_int'),
                'logo_url'=> $SHOP_LOGO.$imgName,
                'uid' => session('uid'),
                'create_time' => time(),
            );
            $shop_id = D('shop')->add($data);
            if(!$shop_id){
                show(0,'门店地址添加失败');
            }else{
                show(1,'门店地址添加成功');
            }

        }


    }

    /**
     * 上传门店Logo图片
     */
    public function uploadShopLogo(){
        if(IS_POST){
            $base64_image = $_POST['img'];
            $UPLOAD_PATH = C('UPLOAD_PATH');
            $CACHE_IMAGE_PATH = C('CACHE_IMAGE_PATH');
            $path = $UPLOAD_PATH.$CACHE_IMAGE_PATH;//图片上传路径
            $imgUrl = upload($base64_image,$path);

            $image = new \Think\Image();
            //open();打开图像资源，通过路径名找到图像
            $image -> open($imgUrl);
            $image -> thumb(161,97,\Think\Image::IMAGE_THUMB_FIXED) ->save($imgUrl);  //按照比例缩小
            show(1,'',$imgUrl);
        }
        
    }

    /**
     * 连锁店类型 修改门店
     */
    public function saveShop(){
        if(IS_POST){
            if(!D('shop')->create()){
                $error_info = D('shop')->getError();
                show(0, $error_info);
            }

             //判断是否更改LOGO图片
            $temp_path =I('post.logo_url_2');//更改LOGO图片的路径
            if(isset($temp_path) && $temp_path){
                $arr=explode('/',$temp_path);
                $imgName = $arr[2];
                $SHOP_LOGO = C('SHOP_LOGO');
                if(!file_exists($SHOP_LOGO)){
                    //检查是否有该文件夹，如果没有就创建，并给予最高权限
                    if(!mkdir($SHOP_LOGO, 0700,true)){
                        show(0,'创建目录失败');
                    }
                }

                if(!rename($temp_path, $SHOP_LOGO.$imgName)){
                    show(0,'图片移动失败');
                }
                $logo_url = $SHOP_LOGO.$imgName;
            }else{
                $logo_url = I('post.logo_url_1');
            }

            if(I('post.company_id',0,'number_int')==''){//判断是那种类型的机构修改的，POST有company_id代表是有分公司的，没有就是没有分公司的连锁类型
                $company_id = intval(D('company')->getCompanyID());
            }else{
                $company_id = I('post.company_id',0,'number_int');
            }
            //把数据入库
            $where = array('id' => I('post.shopId',0,'number_int'));
            $data = array(
                'company_id'=> $company_id,
                'company_name'=> I('post.company_name'),
                'storename'=> I('post.storename'),
                'address'=> I('post.address'),
                'fiex_mobile'=> I('post.fiex_mobile',0,'number_int'),
                'mobile'=> I('post.mobile',0,'number_int'),
                'logo_url'=> $logo_url,
                'uid' => session('uid'),
                'update_time' => time(),
            );
            $shop_id = D('shop') -> where($where) -> save($data);
            if(!$shop_id){
                show(0,'门店地址修改不成功');
            }
        }
    }

    /**
     * 连锁店类型  删除门店
     */
    public function delShop(){
        if(IS_POST){
            $shop_id = I('post.shopId',0,'number_int');
            if(!$shop_id){
                show(0,'门店ID参数不正确');
            }

            $where = array('id' => $shop_id);
            $data = array(
                'status' => intval('1'),//代表删除
                'update_time' => time(),
            );
            $shop_id = D('shop') -> where($where) -> save($data);

            if(!$shop_id){
                show(0,'删除门店不成功');
            }
        }
    }

    /**
     * 预览机构档案
     */
    public function previewCompanyArchive(){
        $companyInfo = D('Company') -> getCompanyInfo();
        $this -> companyInfo = $companyInfo;
        $shopList = D('shop') -> getShopList();
        $this -> shopList = $shopList;
        $this -> display();
    }



}


