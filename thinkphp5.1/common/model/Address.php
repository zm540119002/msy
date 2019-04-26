<?php
namespace common\model;
use think\Model;
use think\Db;
use think\Route;

/**
 * 基础模型器
 */

class Address extends Base{
	// 设置当前模型对应的完整数据表名称
	protected $table = 'address';
	// 设置主键
	protected $pk = 'id';
	// 别名
	protected $alias = 'a';
	// 设置当前模型的数据库连接
    protected $connection = 'db_config_common';

	/**
	 * 编辑 新增和修改
	 * @param string $userId
	 * @return array
	 */
	public function editAddress($userId=''){
          
	}

    /**
     * 获取地址列表
     * @param int $user_id 用户id
     */
	public function getDataList($user_id,$id=''){
        $condition = [
            'where' => [
                ['a.status', '=', 0],
                ['a.user_id','=', $user_id],
            ],
        ];

        if($id!=''){
            $condition['where'][] = ['a.id','=', $id];
            return $this->getInfo($condition);

        }else{
            return $this ->getList($condition);
        }





        if($address && $default){
            $addressList = [];
            foreach ($address as $key => $Info){
                if($Info['is_default'] == 1){
                    $addressList['default'] = $address[$key];
                    break;
                }
            }
            $addressList['list'] = $address;
            return $addressList;

        }else{
            return $address;
        }
    }

}