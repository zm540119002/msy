<?php
namespace Store\Model;
use Think\Model;

class ShopModel extends Model{
    private $_db = '';

    public function __construct() {
        parent::__construct();
        $this->_db = M('shop');
    }

    protected $_validate        =   array(
        array('storename',       'require', '请填写门店名称！'), //默认情况下用正则进行验证
        array('address',         'require', '请填写地址！'), //默认情况下用正则进行验证
        array('fiex_mobile',     'require', '请填写店固定电话！'), //默认情况下用正则进行验证
        array('mobile',          'require', '请填写美容预约对接手机号码！'), //默认情况下用正则进行验证
        array('mobile','isMobile','手机号码格式不正确',0,'function'),
    );

    //增加门店
    public function addStore(){
        
    }

    /**
     *  根据uid查找门店信息
     */
    public function getShopList(){
        $where = array(
            'uid' => session('uid'),
            'status'=> 0, //正常状态 1删除
        );
        $shopList = M('shop') -> where($where)  ->select();
        return $shopList;
    }

    /**
     *  单店类型根据uid查找门店id
     */
    public function getShopId(){
        $where = array(
            'uid' => session('uid'),
            'status'=> 0, //正常状态 1删除
        );
        $shopId = M('shop') -> where($where)  ->getField('id');
        return $shopId;
        
    }

}