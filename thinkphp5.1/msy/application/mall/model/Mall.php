<?php
namespace app\mall\model;

class Mall extends \think\Model {
	// 设置当前模型对应的完整数据表名称
	protected $table = 'mall';
	// 设置主键
	protected $pk = 'id';
	// 设置当前模型的数据库连接
    protected $connection = 'db_config_mall';

	/**编辑
	 */
	public function edit(){
	}
}