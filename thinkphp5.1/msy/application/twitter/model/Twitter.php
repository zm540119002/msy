<?php
namespace app\twitter\model;

class Twitter extends \think\Model {
	// 设置当前模型对应的完整数据表名称
	protected $table = 'twitter';
	// 设置主键
	protected $pk = 'id';
	// 设置当前模型的数据库连接
    protected $connection = 'db_config_twitter';

	/**编辑
	 */
	public function edit(){
	}
}