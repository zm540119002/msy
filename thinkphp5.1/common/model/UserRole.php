<?php
namespace common\model;

class UserRole extends \think\Model {
	// 设置当前模型对应的完整数据表名称
	protected $table = 'user_role';
	// 设置主键
	protected $pk = 'id';
	// 设置当前模型的数据库连接
	protected $connection = 'db_config_common';

	//编辑
	public function edit(){
		$postData = input('post.');
		if($postData['userId'] && intval($postData['userId']) && !empty($postData['roleIds'])){
			$response = $this->where('user_id','=',$postData['userId'])->select();
			$response = $response->toArray();
			//原节点
			$originRoleIds = array_column($response,'role_id');
			//新增节点
			$addRoleIds = empty($originRoleIds)?$postData['roleIds']:array_diff($postData['roleIds'],$originRoleIds);
			if(!empty($addRoleIds)){
				$list = [];
				foreach ($addRoleIds as $val){
					$arr = ['user_id'=>$postData['userId'],'role_id'=>$val];
					$list[] = $arr;
				}
				!empty($list) && $this->saveAll($list);
			}
			if(!empty($originRoleIds)){
				//删除节点
				$delRoleIds = array_diff($originRoleIds,$postData['roleIds']);
				$delRoleIds = array_values($delRoleIds);
				if(!empty($delRoleIds) && !$this->batchDelByRoleIds($delRoleIds)){
					return errorMsg('删除失败',$this->getError());
				}
			}
		}
		return successMsg('成功！');
	}
	//批量删除
	public function batchDelByRoleIds($roleIds){
		$where = [
			['role_id', 'in', $roleIds],
		];
		return $this->where($where)->delete();
	}
}