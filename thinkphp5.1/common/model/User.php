<?php
namespace common\model;

class User extends Base{
	// 设置当前模型对应的完整数据表名称
	protected $table = 'user';
	// 设置主键
	protected $pk = 'id';
	// 别名
	protected $alias = 'u';
	// 设置当前模型的数据库连接
	protected $connection = 'db_config_common';

	//编辑
	public function edit($user){
		$postData = input('post.');
		$validateUser = new \common\validate\User();
		if(!$validateUser->scene('edit')->check($postData)){
			return errorMsg($validateUser->getError());
		}
		if($postData['id'] && intval($postData['id'])){
			$postData['update_time'] = time();
			$this->isUpdate(true)->save($postData);
		}else{
			unset($postData['id']);
			if(isset($user['id']) && $user['id']){
				$postData['type'] = 2;
			}
			$postData['create_time'] = time();
			$this->save($postData);
		}
		if(!$this->getAttr('id')){
			return errorMsg('失败',$this->getError());
		}
		return successMsg('成功！',array('id'=>$this->getAttr('id')));
	}

	//分页查询
	public function pageQuery($userId){
		$where = [
			['status', '=', 0],
			['type', '<>', 0],
		];
		if(isset($userId) && $userId){
			$where[] = ['id', '<>', $userId];
		}
		$keyword = input('get.keyword','');
		if($keyword){
			$where[] = ['name', 'like', '%'.trim($keyword).'%'];
		}
		$field = array(
			'id','name','nickname','mobile_phone','remark',
		);
		$order = 'id';
		$pageSize = (isset($_GET['pageSize']) && intval($_GET['pageSize'])) ?
			input('get.pageSize',0,'int') : config('custom.default_page_size');
		return $this->where($where)->field($field)->order($order)->paginate($pageSize);
	}
}