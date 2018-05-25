<?php
/**
 * Created by PhpStorm.
 * User: Mr.wei
 * Date: 2018/5/25
 * Time: 14:48
 * 地址控制器
 */
namespace app\store\model;

use think\Model;
use think\Db;

class Address extends Model
{
    protected $table = 'address';
    protected $connection = 'db_config_factory';
    protected $pk = 'address_id';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 返回用户地址列表
     * @param $user_id
     * @param bool $is_default
     * @return array
     */
    public function getAddress($user_id, $is_default=false)
    {
        $address = Db::table('msy_factory.address')
            ->where(['user_id'=>$user_id])->order('address_id asc')->select();
        if(!$address||!is_array($address)||count($address)<=0){
            return errorMsg('请添加地址');
        }
        if(!$is_default){
            return successMsg('返回所有地址列表', ['data'=>$address]);
        }
        foreach($address as $v){
            if($v['is_default']==1){
                return successMsg('返回默认地址', [ 'data'=>[0=>$v] ]);
            }
        }
        return successMsg('返回第一条为默认地址', [ 'data'=>[0=>$address[0]] ]);
    }

    public function addAddress($user_id)
    {
        //添加地址
    }

    public function setDefault($user_id)
    {
        //设置默认地址
    }

    public function updateAddress($user_id)
    {
        //修改地址
    }

}