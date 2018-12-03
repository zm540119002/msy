<?php
namespace app\factory\model;

class UserFactoryRole extends \think\model\Pivot {
	// 设置当前模型对应的完整数据表名称
	protected $table = 'user_factory_role';
	// 设置主键
	protected $pk = 'id';
	// 设置当前模型的数据库连接
	protected $connection = 'db_config_factory';

	/**获取用户工厂角色
	 */
	public function getRole($userId,$factoryId){
		$where = [
			['ufr.status','<>',2],
			['ufr.user_id','=',$userId],
			['ufr.factory_id','=',$factoryId],
			['r.status','=',0],
		];
		$field = [
			'r.id','r.name',
		];
		$join = [
			['role r','r.id = ufr.role_id','LEFT'],
		];
		$list = $this->alias('ufr')
			->where($where)
			->field($field)
			->join($join)
			->select();
		return count($list)!=0?$list->toArray():[];
	}
}