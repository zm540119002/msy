<?php
namespace common\model;

use think\Model;

class Node extends Model {
	// 设置当前模型对应的完整数据表名称
	protected $table = 'node';
	// 设置主键
	protected $pk = 'id';
	// 设置当前模型的数据库连接
	protected $connection = 'db_config_common';

	//管理
	public function manage(){
		$data = input('post.');
	}
	//编辑
	public function edit(){
		$postData = input('post.');
		$validateNode = new \common\validate\Node;
		if(!$validateNode->scene('edit')->check($postData)){
			return errorMsg($validateNode->getError());
		}
		return successMsg('成功！');
	}
}