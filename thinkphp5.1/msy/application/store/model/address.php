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

class Address extends Model
{
    protected $table = 'address';
    protected $connection = 'db_config_factory';
    protected $pk = 'address_id';
    protected $autoWriteTimestamp = true; //自动时间戳

    /**
     * 返回用户地址列表
     * @param $user_id
     * @param bool $is_default
     * @return array
     */
    public function getAddress($user_id, $is_default=false)
    {
        $address = $this->where(['user_id'=>$user_id])->order('address_id asc')->select()->toArray();
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

    /**
     * 判断地址是否存在
     * @param $user_id
     * @param $address_id
     * @return array
     */
    public function isAddress($user_id, $address_id)
    {
        $ret = $this->where(['user_id'=>$user_id, 'address_id'=>$address_id])->count();
        if($ret>0){
            return successMsg('地址可用');
        }
        return errorMsg('地址不存在');
    }

    /**
     * 新增地址
     * @param $user_id
     * @param array $data
     * @return array
     */
    public function addAddress($user_id, array $data)
    {
        $count = $this->where(['user_id'=>$user_id])->count();
        if($count>=5){
            return errorMsg('最多只能5个地址');
        }
        $ret = static::create($data, ['user_id', 'consignee', 'phone', 'detail', 'is_default']);
        if($ret){
            return successMsg('新增地址成功');
        }
        return errorMsg('新增地址失败');
    }

    /**
     * 设置默认地址  【暂保留】与updateAddress()方法重复
     * @param $user_id
     * @param $address_id
     * @return array
    public function setDefault($user_id, $address_id)
    {
        $ret = $this->allowField('is_default')
            ->save(['is_default'=>1], ['address_id'=>$address_id, 'user_id'=>$user_id]);
        if($ret){
            return successMsg('设置默认地址成功');
        }
        return errorMsg('设置默认地址失败');
    }*/

    /**
     * 修改地址
     * @param $user_id
     * @param $address_id
     * @param array $data
     * @return array
     */
    public function updateAddress($user_id, $address_id, array $data)
    {
        //修改地址
        $ret = $this->allowField(['consignee', 'phone', 'detail', 'is_default'])
            ->save($data, ['address_id'=>$address_id, 'user_id'=>$user_id]);
        if($ret){
            return successMsg('修改地址成功');
        }
        return errorMsg('修改地址失败');
    }

}