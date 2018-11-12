<?php
namespace app\store\model;

class Manager extends \common\model\Base {
	// 设置当前模型对应的完整数据表名称
	protected $table = 'user';
	// 设置主键
	protected $pk = 'id';
	// 别名
	protected $alias = 'u';
	// 设置当前模型的数据库连接
	protected $connection = 'db_config_common';

	//编辑
	public function edit($factoryId,$factoryType){
		if(!intval($factoryId)){
			return errorMsg('缺少采购商ID');
		}
		$postData = input('post.');
		$postData['name'] = trim($postData['name']);
		$postData['mobile_phone'] = trim($postData['mobile_phone']);
		//用户数据验证
		$validateUser = new \common\validate\User();
		if(!$validateUser->scene('employee')->check($postData)){
			return errorMsg($validateUser->getError());
		}
		//验证用户是否存在
		$managerId = $this->checkUserExistByMobilePhone($postData['mobile_phone']);
		$this->startTrans();//事务开启
		if(!$managerId){//不存在
			$saveData = [
				'type' => 1,
				'name' => $postData['name'],
				'mobile_phone' => $postData['mobile_phone'],
				'create_time' => time(),
			];
			$res = $this->isUpdate(false)->save($saveData);
			if($res===false){
				$this->rollback();//事务回滚
				return errorMsg('失败',$this->getError());
			}
			$managerId = $this->getAttr('id');
		}
		if(isset($postData['id']) && intval($postData['id']) &&
			isset($postData['userFactoryId']) && intval($postData['userFactoryId'])){//修改
			$where = [
				['factory_id','=',$factoryId],
				['id','=',$postData['userFactoryId']],
				['status','=',0],
			];
			$saveData = [
				'user_name' => $postData['name'],
			];
			if($managerId != $postData['id']){//更换管理员账号
				//检验用户是否是本店家管理员
				$userFactoryId = $this->_checkIsManager($managerId,$factoryId);
				if($userFactoryId){//已经是为管理员
					$this->rollback();//事务回滚
					return errorMsg('号码：['.$postData['mobile_phone'].']已经是本店家管理员，请更换手机号码！');
				}
				$saveData['user_id'] = $managerId;
			}
			$modelUserFactory = new \common\model\UserFactory();
			$res = $modelUserFactory->isUpdate(true)->save($saveData,$where);
			if($res===false){
				$this->rollback();//事务回滚
				return errorMsg('失败',$modelUserFactory->getError());
			}
			$postData['user_factory_id'] = $postData['userFactoryId'];
		}else{//新增
			//检验用户是否是本店家管理员
			$userFactoryId = $this->_checkIsManager($managerId,$factoryId);
			if($userFactoryId){//已经是为管理员
				$this->rollback();//事务回滚
				return errorMsg('号码：['.$postData['mobile_phone'].']已经是本店家管理员，请更换手机号码！');
			}
			$saveData = [
				'type' => 2,
				'user_id' => $managerId,
				'user_name' => $postData['name'],
				'factory_id' => $factoryId,
				'factory_type' => $factoryType,
			];
			$modelUserFactory = new \common\model\UserFactory();
			$res = $modelUserFactory->isUpdate(false)->save($saveData);
			if($res===false){
				$this->rollback();//事务回滚
				return errorMsg('失败',$this->getError());
			}
			$userFactoryId = $modelUserFactory->getAttr('id');
			$postData['user_factory_id'] = $userFactoryId;
		}
		$postData['id'] = $managerId;
		$this->commit();//事务提交
		return successMsg('成功！',$postData);
	}

	//列表
	public function getList($factoryId){
		$modelUserFactory = new \common\model\UserFactory();
		$where = [
			['uf.factory_id','=',$factoryId],
			['uf.status','=',0],
			['uf.type','=',2],
			['u.status','=',0],
		];
		$field = [
			'u.id','u.mobile_phone',
			'uf.id user_factory_id','uf.user_name name',
		];
		$join = [
			['user u','u.id = uf.user_id','left'],
		];
		$list = $modelUserFactory->alias('uf')->field($field)->join($join)->where($where)->select();
		return count($list)!=0?$list->toArray():[];
	}

	//删除
	public function del($factoryId,$tag=true){
		$userFactoryId = input('post.userFactoryId',0);
		if(!$userFactoryId){
			return errorMsg('参数错误');
		}
		$where = [
			['id', '=', $userFactoryId],
			['factory_id', '=', $factoryId],
			['status', '=', 0],
			['type', '=', 2],
		];
		$modelUserFactory = new \common\model\UserFactory();
		if($tag){//标记删除
			$result = $modelUserFactory->where($where)->setField('status',2);
		}else{
			$result = $modelUserFactory->where($where)->delete();
		}
		if(!$result){
			return errorMsg('失败',$modelUserFactory->getError());
		}
		return successMsg('成功');
	}

	/**检验用户是否是本店家管理员
	 */
	private function _checkIsManager($userId,$factoryId){
		$modelUserFactory = new \common\model\UserFactory();
		$where = [
			['user_id','=',$userId],
			['factory_id','=',$factoryId],
			['status','<>',2],
			['type','=',2],
		];
		return $modelUserFactory->where($where)->value('id');
	}
}