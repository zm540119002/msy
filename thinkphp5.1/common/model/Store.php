<?php
namespace common\model;

/**基础模型器
 */
class Store extends Base {
	// 设置当前模型对应的完整数据表名称
	protected $table = 'store';
	// 设置主键
	protected $pk = 'id';
	// 别名
	protected $alias = 's';
	// 设置当前模型的数据库连接
    protected $connection = 'db_config_common';

	/**编辑
	 */
	public function edit($factoryId,$userId=0){
		$data = input('post.');
		$data['factory_id'] = $factoryId;
		$validate = validate('\common\validate\Store');
		if(input('?post.id')){
			if(!$result = $validate->scene('edit')->check($data)) {
				return errorMsg($validate->getError());
			}
			$data['update_time'] = time();
			$result = $this->allowField(true)->save($data,['id' => $data['store_id'],'factory_id'=>$factoryId]);
			if(false !== $result){
				return successMsg("成功");
			}
			return errorMsg('失败',$this->getError());
		}else{
			if(!$result = $validate->scene('add')->check($data)) {
				return errorMsg($validate->getError());
			}
			$this->startTrans();
			$data['create_time'] = time();
			$result = $this->allowField(true)->save($data);
			if(!$result){
				$this->rollback();//事务回滚
				return errorMsg('失败');
			}
			$modelUserStore = new \common\model\UserStore();
			$storeId = $this->getAttr('id');
			$postData['type'] = 2;
			$postData['factory_id'] = $factoryId;
			$postData['user-id'] = $userId;
			$postData['store_id'] = $storeId;
			$result = $modelUserStore->save($postData);
			if(!$result){
				$this->rollback();//事务回滚
				return errorMsg('失败',$this->getError());
			}
			$this->commit();//事务提交
			return successMsg('提交申请成功');
		}
	}
	
	//设置默认店铺
	public function setDefaultStore($factoryId=''){
		if(request()->isAjax()){
			$id = (int)input('post.id');
			if(!$id){
				return errorMsg('参数错误');
			}
			$this->startTrans();
			$data = array('is_default' => 1);
			$result = $this->allowField(true)->save($data,['id' => $id,'factory_id'=>$factoryId]);
			if(false === $result){
				$this->rollback();
				return errorMsg('修改默认失败');
			}
			$where = [
				['id','<>',$id],
				['factory_id','=',$factoryId],
			];
			$result = $this ->where($where)->setField('is_default',0);
			if(false === $result){
				$this->rollback();
				return errorMsg('修改失败');
			}
			$this->commit();
			return successMsg("已选择");
		}
	}

	/**检查店铺是否属于此厂商
	 */
	public function checkStoreExist($id,$factoryId){
		$where = [
			['id','=',$id],
			['factory_id','=',$factoryId],
		];
		$count = $this->where($where)->count();
		if($count){
			return true;
		}else{
			return false;
		}
	}

	//设置店长
	public function setManager($factoryId){
		$postData = input('post.');
		$storeId = (int)$postData['id'];
		if(!$storeId){
			return errorMsg('缺少店铺ID');
		}
		if($postData['mobile_phone']){//手机号存在
			//验证用户是否存在
			$userId = $this->checkUserExistByMobilePhone($postData['mobile_phone']);
			$this->startTrans();//事务开启
			if(!$userId){//不存在
				unset($postData['id']);
				$postData['type'] = 1;
				$postData['nickname'] = trim($postData['name']);
				$postData['create_time'] = time();
				$modelUser = new \common\model\User();
				$res = $modelUser->save($postData);
				if($res===false){
					$modelUser->rollback();//事务回滚
					return errorMsg('失败',$modelUser->getError());
				}
				$userId = $modelUser->getAttr('id');
			}
			//验证是否存在店长
			$userStoreId = $this->checkManagerExist($factoryId,$storeId);
			$modelUserStore = new \common\model\UserStore();
			if($userStoreId){//存在测删除
				$where = [
					['id', '=', $userStoreId],
				];
				$modelUserStore->del($where,false);
			}
			$postData['type'] = 3;
			$postData['user_id'] = $userId;
			$postData['factory_id'] = $factoryId;
			$postData['store_id'] = $storeId;
			$res = $modelUserStore->save($postData);
			if($res===false){
				$this->rollback();//事务回滚
				return errorMsg('失败',$this->getError());
			}
			$userStoreId = $modelUserStore->getAttr('id');
			$this->commit();//事务提交

			$postData['id'] = $userId;
			$postData['user_store_id'] = $userStoreId;
			return successMsg('成功！',$postData);
		}else{//手机号不存在
			//验证是否存在店长
			$userStoreId = $this->checkManagerExist($factoryId,$storeId);
			$modelUserStore = new \common\model\UserStore();
			if($userStoreId){//存在测删除
				$where = [
					['id', '=', $userStoreId],
				];
				$modelUserStore->del($where,false);
			}
			return successMsg('删除成功！');
		}
	}

	/**验证用户是否为店长
	 */
	private function checkManagerExist($factoryId,$storeId){
		$modelUserStore = new \common\model\UserStore();
		$where = [
			['store_id','=',$storeId],
			['factory_id','=',$factoryId],
			['status','<>',2],
			['type','=',3],
		];
		return $modelUserStore->where($where)->value('id');
	}
}