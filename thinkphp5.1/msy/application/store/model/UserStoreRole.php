<?php
namespace app\store\model;

class UserStoreRole extends \think\model\Pivot {
	// 设置当前模型对应的完整数据表名称
	protected $table = 'user_store_role';
	// 设置主键
	protected $pk = 'id';
	// 设置当前模型的数据库连接
	protected $connection = 'db_config_store';

	/**获取用户工厂角色
	 */
	public function getRole($userId,$storeId){
		$where = [
			['usr.status','<>',2],
			['usr.user_id','=',$userId],
			['usr.store_id','=',$storeId],
			['r.status','=',0],
		];
		$field = [
			'r.id','r.name',
		];
		$join = [
			['role r','r.id = usr.role_id','LEFT'],
		];
		$list = $this->alias('usr')
			->where($where)
			->field($field)
			->join($join)
			->select();
		return count($list)?$list->toArray():[];
	}
}