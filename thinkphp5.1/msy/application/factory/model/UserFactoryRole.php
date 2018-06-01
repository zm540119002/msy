<?php
namespace app\factory\model;

class UserFactoryRole extends \think\model\Pivot {
	// 设置当前模型对应的完整数据表名称
	protected $table = 'user_factory_role';
	// 设置主键
	protected $pk = 'id';
	// 设置当前模型的数据库连接
	protected $connection = 'db_config_factory';

	/**查询多条数据
	 */
	public function getList($where=[],$field=['*'],$join=[],$order=[],$limit=''){
		$_where = array(
			'ufr.status' => 0,
		);
		$_join = array(
		);
		$where = array_merge($_where, $where);
		$list = $this->alias('ufr')
			->where($where)
			->field($field)
			->join(array_merge($_join,$join))
			->order($order)
			->limit($limit)
			->select();
		return count($list)?$list->toArray():[];
	}
}