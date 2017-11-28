<?php
namespace Store\Model;
use Think\Model;

class CompanyModel extends Model{

    private $_db = '';

    public function __construct() {
        parent::__construct();
        $this->_db = M('Company');
    }

    protected $_validate        =   array(
        array('name',               'require', '机构名或公司不能为空！'), //默认情况下用正则进行验证
        array('organizajc',         'require', '机构简称不能为空！'), //默认情况下用正则进行验证
        array('register_name',       'require', '名字不能为空！'), //默认情况下用正则进行验证
        array('organiza_mobile',     'require', '号码不能为空！'), //默认情况下用正则进行验证
        array('consignee',          'require', '请填收货人名字！'), //默认情况下用正则进行验证
        array('consignee_mobile',   'require', '请填收货人号码！'), //默认情况下用正则进行验证
        array('province',           'require', '请选择省份！'), //默认情况下用正则进行验证
        array('city',              'require', '请选择城市！'), //默认情况下用正则进行验证
        array('area',              'require', '请选择地区！'), //默认情况下用正则进行验证
        array('address',           'require', '请填具体地址！'), //默认情况下用正则进行验证

        array('applicants',     'require', '申请人名字不能为空！'), //默认情况下用正则进行验证
        array('applicants_mobile',     'require', '申请人电话不能为空！'), //默认情况下用正则进行验证



//        array('organizaName', '', '已存在该机构，请核实...', 0, 'unique', 1), // 在新增的时候验证name字段是否唯一
        array('organiza_mobile','isMobile','手机号码格式不正确',0,'function'),
        array('consignee_mobile','isMobile','手机号码格式不正确',0,'function'),
        array('applicants_mobile','isMobile','手机号码格式不正确',0,'function'),


    );

    /**
     *  根据id 更改机构状态
     */
    public function updateAuth_statusById($id,$status){
        $where = array(
            'uid' => $id,
        );
        $data = array(
            'auth_status' => $status
        );

        $this->_db-> where($where) -> save($data);
       
    }

    /**
     *  根据uid查找机构id
     */
    public function getCompanyID(){
        $where = array(
            'uid' => session('uid'),
        );
        $company_id = $this->_db-> where($where) -> getField('id');
        return $company_id;
    }

    /**
     *  根据pid查找分公司
     */
    public function getCompany(){
        $pid  = intval($this->getCompanyID());
        $where = array(
            'pid'=> $pid,
            'status'=> 0, //正常状态
            'type'=>1, //分公司类型
        );
        $companyList = M('company') -> where($where) -> Field('id,name,type') ->select();
        return $companyList;
        //$this -> companyList = $companyList;
    }

    /**
     * 查找机构基本信息
     * @return mixed
     */
    public function getCompanyInfo(){
        $where = array(
            'uid' => session('uid'),
            'type' => 0,
            'status' => 0,
        );
        $field = array(
            'name','organizajc','description','imgurl1','imgurl2','imgurl3',
            'imgurl4','imgurl5','imgurl6','imgurl7','imgurl8','imglogourl','shop_type'
        );
        $companyInfo = M('company') -> where($where) -> field($field) ->find();
        //判断机构的图片是否为空
        if(($companyInfo['imgurl1']||$companyInfo['imgurl2']
                ||$companyInfo['imgurl1']||$companyInfo['imgurl1']
                ||$companyInfo['imgurl1']||$companyInfo['imgurl1']
                ||$companyInfo['imgurl1']||$companyInfo['imgurl1'])!= ''){
            $companyInfo['figureSign'] = false;
        }else{
            $companyInfo['figureSign'] = true;
        }
        return $companyInfo;
    }

}