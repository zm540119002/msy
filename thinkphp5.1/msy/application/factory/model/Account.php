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
		if($postData['id'] && intval($postData['id'])){//修改用户
			$res = $this->isUpdate(true)->save($postData);
			if($res===false){
				$this->rollback();//回滚事务
				return errorMsg('更新失败',$this->getError());
			}
		}else{//新增用户
			$postData['factory_id'] = $factoryId;
			unset($postData['id']);
			$res = $this->isUpdate(false)->save($postData);
			if($res===false){
				$this->rollback();//回滚事务
				return errorMsg('新增失败',$this->getError());
			}
			$postData['id'] = $this->getAttr('id');
			$user = $this->get($postData['id']);
			if(!empty($user)){
				//新增用户工厂
				$data = [
					'type' => 2,
				];
				$res = $user->factories()->attach($factoryId,$data);
				if(!count($res)){
					$this->rollback();//回滚事务
					return errorMsg('新增失败');
				}
				//新增用户工厂角色
				if(is_array($postData['userFactoryRoleIds']) && !empty($postData['userFactoryRoleIds'])){
					foreach ($postData['userFactoryRoleIds'] as $val){
						$data = [
							'factory_id' => $factoryId,
						];
						$res = $user->roles()->attach($val,$data);
						if(!count($res)){
							$this->rollback();//回滚事务
							return errorMsg('新增失败');
						}
					}
				}
				//新增用户工厂组织
				if(isset($postData['userFactoryOrganizeId']) && intval($postData['userFactoryOrganizeId'])){
					$data = [
						'factory_id' => $factoryId,
					];
					$res = $user->organizes()->attach($postData['userFactoryOrganizeId'],$data);
					if(!count($res)){
						$this->rollback();//回滚事务
						return errorMsg('新增失败');
					}
				}
			}
		}
		$this->commit();//提交事务
		return successMsg('成功！',$postData);
	}

	//用户-工厂-关联模型
	public function factories(){
		return $this->belongsToMany('Factory','UserFactory','factory_id','user_id');
	}

	//用户-工厂角色-关联模型
	public function roles(){
		return $this->belongsToMany('Role','UserFactoryRole','role_id','user_id');
	}

	//用户-工厂-组织-关联模型
	public function organizes(){
		return $this->belongsToMany('Organize','UserFactoryOrganize','organize_id','user_id');
	}

	//详情
	public function detail($factoryId){
		if(!intval($factoryId)){
			return errorMsg('参数错误');
		}
		//用户信息
		$where = [
			['status', '<>', 2],
		];
		$id = input('id');
		if(!intval($id)){
			return errorMsg('参数错误');
		}
		$where[] = ['id','=',$id];
		$field = [
			'u.id','u.name','u.nickname','u.mobile_phone','u.status',
		];
		$info = $this->alias('u')->field($field)->where($where)->find();
		//用户工厂角色
		$modelUserFactoryRole = new \app\factory\model\UserFactoryRole();
		$where = [
			'ufr.user_id' => $info['id'],
			'ufr.factory_id' => $factoryId,
		];
		$field = [
			'r.id','r.name',
		];
		$join = [
			['role r','r.id = ufr.role_id','LEFT'],
		];
		$info['role'] = $modelUserFactoryRole->getList($where,$field,$join);
		return $info?$info->toArray():[];
	}

	//获取列表
	public function getList($factoryId){
		$modelUserFactory = new \app\factory\model\UserFactory();
		$where = [
			['uu.status','<>',2],
			['u.factory_id','=',$factoryId],
			['u.type','=',2],
		];
		$keyword = input('get.keyword','');
		if($keyword){
			$where[] = ['uu.name|uu.mobile_phone', 'like', '%'.trim($keyword).'%',];
		}
		$field = [
			'uu.id','uu.name','uu.nickname','uu.mobile_phone','uu.status','u.is_default',
		];
		$join = [
			['common.user uu','uu.id = u.user_id','LEFT'],
		];
		$list = $modelUserFactory->getList($where,$field,$join);
		$modelUserFactoryRole = new \app\factory\model\UserFactoryRole();
		$field = [
			'r.id','r.name',
		];
		$join = [
			['role r','r.id = ufr.role_id','LEFT'],
		];
		foreach ($list as &$value){
			$where = [
				'ufr.user_id' => $value['id'],
				'ufr.factory_id' => $factoryId,
			];
			$value['role'] = $modelUserFactoryRole->getList($where,$field,$join);
		}
		return count($list)?$list:[];
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