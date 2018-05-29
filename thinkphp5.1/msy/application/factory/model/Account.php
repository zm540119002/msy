<?php
namespace app\factory\model;

class Account extends \think\Model {
	// 设置当前模型对应的完整数据表名称
	protected $table = 'user';
	// 设置主键
	protected $pk = 'id';
	// 设置当前模型的数据库连接
	protected $connection = 'db_config_common';

	//编辑
	public function edit($factoryId){
		if(!intval($factoryId)){
			return errorMsg('缺少供应商ID');
		}
		$postData = input('post.');
		//用户数据验证
		$validateUser = new \common\validate\User();
		if(!$validateUser->scene('edit')->check($postData)){
			return errorMsg($validateUser->getError());
		}
		//开启事务
		$this->startTrans();
		//修改用户
		if($postData['id'] && intval($postData['id'])){
			$res = $this->isUpdate(true)->save($postData);
			if($res===false){
				$this->rollback();//回滚事务
				return errorMsg('更新失败',$this->getError());
			}
			$roles = $this->roles();
			return $roles;
		}else{//新增用户
			$postData['factory_id'] = $factoryId;
			unset($postData['id']);
			$res = $this->isUpdate(false)->save($postData);
			if($res===false){
				$this->rollback();//回滚事务
				return errorMsg('新增失败',$this->getError());
			}
			$postData['id'] = $this->getAttr('id');
			//新增用户工厂
			$user = $this->get($postData['id']);
			$userFactory = $user->userFactory();
			if(empty($userFactory)){
				$data = [
					'user_id' => $postData['id'],
					'factory_id' => $factoryId,
					'type' => 2,
				];
				$userFactory->save($data);
			}
			//新增用户工厂角色
			if(is_array($postData['userFactoryRoleIds']) && !empty($postData['userFactoryRoleIds'])){
				foreach ($postData['userFactoryRoleIds'] as $key){
					$data = [];
					$data[] = [
						'user_id' => $postData['id'],
						'factory_id' => $factoryId,
						'role_id' => $key,
					];
				}
				$user->UserFactoryRole()->saveAll($data);
			}
			//新增用户工厂组织
			if(isset($postData['userFactoryOrganizeId']) && intval($postData['userFactoryOrganizeId'])){
				$data = [
					'user_id' => $postData['id'],
					'factory_id' => $factoryId,
					'organize_id' => $postData['userFactoryOrganizeId'],
				];
				$user->userFactoryOrganize()->save($data);
			}
		}
		$this->commit();//提交事务
		return successMsg('成功！',$postData);
	}

	//用户-工厂-关系表
	public function userFactory(){
		return $this->hasMany('UserFactory','factory_id','id');
	}

	//用户-工厂-角色-关系表
	public function UserFactoryRole(){
		return $this->hasMany('UserFactoryRole','role_id','id');
	}

	//用户-工厂-组织-关系表
	public function userFactoryOrganize(){
		return $this->hasOne('UserFactoryOrganize','id');
	}

	//获取列表
	public function getList($factoryId){
		$where = [
			['status', '=', 0],
			['factory_id', '=', $factoryId],
		];
		$field = array(
			'id','name',
		);
		$order = 'id';
		$list = $this->where($where)->field($field)->order($order)->select()->toArray();
		return empty($list)?[]:$list;
	}

	//删除
	public function del($factoryId,$tag=true){
		if(!intval($factoryId)){
			return errorMsg('参数错误');
		}
		$where = [
			['status', '=', 0],
			['factory_id', '=', $factoryId],
		];
		$id = input('post.id');
		if(!intval($id)){
			return errorMsg('参数错误');
		}
		$where[] = ['id', '=', $id];
		if($tag){//标记删除
			$result = $this->where($where)->setField('status',2);
		}else{
			$result = $this->where($where)->delete();
		}
		if(!$result){
			return errorMsg('失败',$this->getError());
		}
		return successMsg('成功');
	}
}