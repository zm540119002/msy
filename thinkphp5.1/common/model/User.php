<?php
namespace common\model;

use think\Model;
use think\Db;

class User extends Model {
	// 设置当前模型对应的完整数据表名称
	protected $table = 'user';
	// 设置主键
	protected $pk = 'id';
	// 设置当前模型的数据库连接
	protected $connection = 'db_config_common';

	/**新增
	 */
	public function add(){
		$data = input('post.');
		$validate = new \common\validate\User;
		if(!$result = $validate->scene('add')->check($data)) {
			return errorMsg($validate->getError());
		}
	}
}