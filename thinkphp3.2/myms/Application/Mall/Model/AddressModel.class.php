<?php
namespace Mall\Model;
use Think\Model;
class AddressModel extends Model {
    protected $tableName = 'address';
    protected $tablePrefix = '';
    protected $connection = 'DB_CONFIG_MYH';

  //根据Uid获取默认地址
    public function getUserAddressByUid($uid){
        $where['user_id'] = $uid;
        $where['is_default'] = array('eq',1);
        $address = D('Address') -> where($where) -> find();
        return $address;
    }

    //根据地址id获取地址
    public function getUserAddressById($uid,$addressId){
        $where['id'] = $uid;
        $where['id'] = $addressId;
        $address = D('Address') -> where($where) -> find();
        return $address;
    }
    //根据地址Uid获取地址列表List
    public function getUserAddressListByUid($uid){
        $where['user_id'] = $uid;
        $addressList = D('Address') -> where($where) -> select();
        return $addressList;
    }

    //除了你最后设置默认地址外，其他的地址状态都要改为0
    public function updateAddressNotDefault($uid,$addressId){
         $where['id']  = array('neq',$addressId);
         $where['$uid']  = $uid;
         $return = D('Address') ->where($where)->setField('is_default',0);
         return $return;
    }

        

}