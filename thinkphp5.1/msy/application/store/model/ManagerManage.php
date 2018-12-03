<?php
namespace app\store\model;

class ManagerManage extends \common\model\Base {
	// 设置当前模型对应的完整数据表名称
	protected $table = 'user';
	// 设置主键
	protected $pk = 'id';
	// 别名
	protected $alias = 'u';
	// 设置当前模型的数据库连接
	protected $connection = 'db_config_common';

	//编辑
	public function editStoreEmployee($storeId){
		if(!intval($storeId)){
			return errorMsg('缺少店铺ID');
		}
		$postData = input('post.');
		//用户数据验证
		$validateUser = new \common\validate\User();
		if(!$validateUser->scene('employee')->check($postData)){
			return errorMsg($validateUser->getError());
		}
		$postData['name'] = trim($postData['name']);
		$postData['mobile_phone'] = trim($postData['mobile_phone']);
		//验证用户是否存在
		$userId = $this->checkUserExistByMobilePhone($postData['mobile_phone']);
		if(!$userId){//不存在
			$saveData = [
				'name' => $postData['name'],
				'mobile_phone' => $postData['mobile_phone'],
				'create_time' => time(),
			];
			$res = $this->isUpdate(false)->save($saveData);
			if($res===false){
				$this->rollback();//事务回滚
				return errorMsg('失败',$this->getError());
			}
			$userId = $this->getAttr('id');
		}
		$this->startTrans();//事务开启
		if($postData['id'] && intval($postData['id']) && $postData['user_store_id'] && intval($postData['user_store_id'])){//修改
			if($postData['id'] != $userId){
				$this->rollback();//回滚事务
				return errorMsg('不能修改手机号码！');
			}
			$saveData = [
				'user_name' => $postData['name'],
				'post' => $postData['post'],
				'duty' => $postData['duty'],
			];
			$where = [
				'id' => $postData['user_store_id'],
				'store_id' => $storeId,
				'type' => 4,
				'status' => 0,
			];
			$modelUserStore = new \common\model\UserStore();
			$res = $modelUserStore->isUpdate(true)->save($saveData,$where);
			if($res===false){
				$this->rollback();//事务回滚
				return errorMsg('失败',$modelUserStore->getError());
			}
			if(!empty($postData['nodeIds'])){
				$modelUserStoreNode = new \common\model\UserStoreNode();
				$config = [
					'field' => [
						'usn.node_id',
					],'where' => [
						['usn.status','=',0],
						['usn.user_id','=',$userId],
						['usn.store_id','=',$storeId],
					],
				];
				$userStoreNodeList = $modelUserStoreNode->getlist($config);
				$oldNodeIds = array_unique(array_column($userStoreNodeList,'node_id'));
				if(empty($oldNodeIds)){
					$addNodeIds = $postData['nodeIds'];
					$delNodeIds = [];
				}else{
					$addNodeIds = array_diff($postData['nodeIds'],$oldNodeIds);
					$delNodeIds = array_diff($oldNodeIds,$postData['nodeIds']);
				}
				if(!empty($addNodeIds)){
					$saveData = [];
					foreach ($addNodeIds as $node){
						$saveData[] = [
							'user_id' => $userId,
							'store_id' => $storeId,
							'node_id' => $node,
						];
					}
					$res = $modelUserStoreNode->isUpdate(false)->saveAll($saveData);
					if(false===$res){
						$this->rollback();//回滚事务
						return errorMsg($modelUserStoreNode->getError());
					}
				}
				if(!empty($delNodeIds)){
					$where = [
						['user_id','=',$userId],
						['store_id','=',$storeId],
					];
					$where[] = ['node_id','in',$delNodeIds];
					$res = $modelUserStoreNode->where($where)->delete();
					if(!$res){
						$modelUserStoreNode->rollback();//回滚事务
						return errorMsg('失败',$modelUserStoreNode->getError());
					}
				}
			}
		}else{//新增
			//检查员工是否存在
			$userStoreId = $this->_checkStoreEmployeeExist($userId,$storeId);
			if($userStoreId){
				$this->rollback();//事务回滚
				return errorMsg('号码：['.$postData['mobile_phone'].']已经是本店家员工，请更换手机号码！');
			}
			$saveData = [
				'type' => 4,
				'user_id' => $userId,
				'user_name' => $postData['name'],
				'store_id' => $storeId,
				'post' => $postData['post'],
				'duty' => $postData['duty'],
			];
			$modelUserStore = new \common\model\UserStore();
			$res = $modelUserStore->isUpdate(false)->save($saveData);
			if($res===false){
				$this->rollback();//事务回滚
				return errorMsg('失败',$modelUserStore->getError());
			}
			$userStoreId = $modelUserStore->getAttr('id');
			if(!empty($postData['nodeIds'])){
				//新增权限节点
				$list = [];
				foreach ($postData['nodeIds'] as $value){
					$list[] = [
						'user_id'=>$userId,
						'store_id'=>$storeId,
						'node_id'=>$value,
					];
				}
				$modelUserStoreNode = new \common\model\UserStoreNode();
				$res = $modelUserStoreNode->isUpdate(false)->saveAll($list);
				if($res===false){
					$this->rollback();//事务回滚
					return errorMsg('失败',$modelUserStoreNode->getError());
				}
			}
			$postData['id'] = $userId;
			$postData['user_store_id'] = $userStoreId;
		}
		$this->commit();//提交事务
		return successMsg('成功！',$postData);
	}

	/**编辑店铺收货人信息
	 */
	public function editStoreConsigneeInfo($storeId){
		$modelStore = new \common\model\Store();
		$where = [
			['id', '=', $storeId],
			['status', '=', 0],
		];
		$postData = input('post.');
		list($postData['province'],$postData['city'],$postData['area']) = $postData['region'];
		$res = $modelStore->isUpdate(true)->save($postData,$where);
		if($res===false){
			return errorMsg('失败',$modelStore->getError());
		}
		return successMsg('成功');
	}

	//编辑
	public function editShopEmployee($storeId){
		if(!intval($storeId)){
			return errorMsg('缺少店铺ID');
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
		$userId = $this->checkUserExistByMobilePhone($postData['mobile_phone']);
		$this->startTrans();//事务开启
		if(!$userId){//不存在
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
			$userId = $this->getAttr('id');
		}
		if($postData['id'] && intval($postData['id']) && $postData['user_shop_id'] && intval($postData['user_shop_id'])){//修改
			if($postData['id'] != $userId){
				$this->rollback();//回滚事务
				return errorMsg('不能修改手机号码！');
			}
			$saveData = [
				'shop_id' => $postData['shop_id'],
				'user_name' => $postData['name'],
				'post' => $postData['post'],
				'duty' => $postData['duty'],
			];
			$where = [
				['id','=',$postData['user_shop_id']],
				['user_id','=',$userId],
				['store_id','=',$storeId],
				['type','=',4],
			];
			$modelUserShop = new \app\store\model\UserShop();
			$res = $modelUserShop->isUpdate(true)->save($saveData,$where);
			if($res===false){
				$this->rollback();//事务回滚
				return errorMsg('失败',$modelUserShop->getError());
			}
			if(!empty($postData['nodeIds'])){
				$modelUserShopNode = new \app\store\model\UserShopNode();
				$config = [
					'field' => [
						'usn.node_id',
					],'where' => [
						['usn.status','=',0],
						['usn.user_id','=',$userId],
						['usn.store_id','=',$storeId],
					],
				];
				$userShopNodeList = $modelUserShopNode->getList($config);
				$oldNodeIds = array_unique(array_column($userShopNodeList,'node_id'));
				if(empty($oldNodeIds)){
					$addNodeIds = $postData['nodeIds'];
					$delNodeIds = [];
				}else{
					$addNodeIds = array_diff($postData['nodeIds'],$oldNodeIds);
					$delNodeIds = array_diff($oldNodeIds,$postData['nodeIds']);
				}
				if(!empty($addNodeIds)){
					$data = [];
					foreach ($addNodeIds as $node){
						$data[] = [
							'user_id' => $userId,
							'store_id' => $storeId,
							'node_id' => $node,
							'shop_id'=>$postData['shop_id'],
						];
					}
					$res = $modelUserShopNode->isUpdate(false)->saveAll($data);
					if(false===$res){
						$this->rollback();//回滚事务
						return errorMsg($modelUserShopNode->getError());
					}
				}
				if(!empty($delNodeIds)){
					$where = [
						['user_id','=',$userId],
						['store_id','=',$storeId],
					];
					$where[] = ['node_id','in',$delNodeIds];
					$res = $modelUserShopNode->where($where)->delete();
					if(!$res){
						$this->rollback();//回滚事务
						return errorMsg('失败',$modelUserShopNode->getError());
					}
				}
			}
		}else{//新增
			//检查员工是否存在
			$userShopId = $this->_checkShopEmployeeExist($userId,$storeId,$postData['shop_id']);
			if($userShopId){
				$this->rollback();//事务回滚
				return errorMsg('号码：['.$postData['mobile_phone'].']已经是本门店员工，请更换手机号码！');
			}
			$saveData = [
				'type' => 4,
				'user_id' => $userId,
				'user_name' => $postData['name'],
				'store_id' => $storeId,
				'shop_id' => $postData['shop_id'],
				'post' => $postData['post'],
				'duty' => $postData['duty'],
			];
			$modelUserShop = new \app\store\model\UserShop();
			$res = $modelUserShop->isUpdate(false)->save($saveData);
			if($res===false){
				$this->rollback();//事务回滚
				return errorMsg('失败',$modelUserShop->getError());
			}
			$userShopId = $modelUserShop->getAttr('id');
			if(!empty($postData['nodeIds'])){
				//新增权限节点
				$saveData = [];
				foreach ($postData['nodeIds'] as $value){
					$saveData[] = [
						'user_id'=>$userId,
						'store_id'=>$storeId,
						'shop_id'=>$postData['shop_id'],
						'node_id'=>$value,
					];
				}
				$modelUserShopNode = new \app\store\model\UserShopNode();
				$res = $modelUserShopNode->isUpdate(false)->saveAll($saveData);
				if($res===false){
					$this->rollback();//事务回滚
					return errorMsg('失败',$modelUserShopNode->getError());
				}
			}
			$postData['id'] = $userId;
			$postData['user_shop_id'] = $userShopId;
		}
		$this->commit();//事务提交
		return successMsg('成功！',$postData);
	}

	//删除店铺员工
	public function delStoreEmployee($storeId,$tag=true){
		$userStoreId = input('post.user_store_id',0);
		if(!$userStoreId){
			return errorMsg('参数错误');
		}
		$userId = input('post.id',0);
		$id = input('post.id',0);
		if(!$id){
			return errorMsg('参数错误');
		}
		$where = [
			['id', '=', $userStoreId],
			['user_id', '=', $userId],
			['store_id', '=', $storeId],
			['status', '=', 0],
			['type', '=', 4],
		];
		$modelUserStore = new \common\model\UserStore();
		$modelUserStore->startTrans();//事务开启
		if($tag){//标记删除
			$result = $modelUserStore->where($where)->setField('status',2);
		}else{
			$result = $modelUserStore->where($where)->delete();
		}
		if($result===false){
			$modelUserStore->rollback();//事务回滚
			return errorMsg('失败',$modelUserStore->getError());
		}
		$modelUserStoreNode = new \common\model\UserStoreNode();
		$where = [
			['user_id', '=', $userId],
			['store_id', '=', $storeId],
			['status', '=', 0],
		];
		if($tag){//标记删除
			$result = $modelUserStoreNode->where($where)->setField('status',2);
		}else{
			$result = $modelUserStoreNode->where($where)->delete();
		}
		if($result===false){
			$modelUserStore->rollback();//事务回滚
			return errorMsg('失败',$modelUserStoreNode->getError());
		}
		$modelUserStore->commit();//事务提交
		return successMsg('成功');
	}

	//删除门店员工
	public function delShopEmployee($storeId,$tag=true){
		$postData = input('post.');
		if(!intval($postData['id'])){
			return errorMsg('参数错误');
		}
		if(!intval($postData['user_shop_id'])){
			return errorMsg('参数错误');
		}
		if(!intval($postData['shop_id'])){
			return errorMsg('参数错误');
		}
		$where = [
			['id', '=', $postData['user_shop_id']],
			['user_id', '=', $postData['id']],
			['store_id', '=', $storeId],
			['shop_id', '=', $postData['shop_id']],
			['status', '=', 0],
			['type', '=', 4],
		];
		$modelUserShop = new \app\store\model\UserShop();
		$modelUserShop->startTrans();//事务开启
		if($tag){//标记删除
			$result = $modelUserShop->where($where)->setField('status',2);
		}else{
			$result = $modelUserShop->where($where)->delete();
		}
		if($result===false){
			$modelUserShop->rollback();//事务回滚
			return errorMsg('失败',$modelUserShop->getError());
		}
		if(!empty($postData['nodeIds'])){
			$modelUserShopNode = new \app\store\model\UserShopNode();
			$where = [
				['user_id', '=', $postData['id']],
				['store_id', '=', $storeId],
				['shop_id', '=', $postData['shop_id']],
				['status', '=', 0],
			];
			if($tag){//标记删除
				$result = $modelUserShopNode->where($where)->setField('status',2);
			}else{
				$result = $modelUserShopNode->where($where)->delete();
			}
			if($result===false){
				$modelUserShop->rollback();//事务回滚
				return errorMsg('失败',$modelUserShopNode->getError());
			}
		}
		$modelUserShop->commit();//事务提交
		return successMsg('成功');
	}

	/**检查店铺员工是否存在
	 */
	private function _checkStoreEmployeeExist($userId,$storeId){
		$modelUserStore = new \common\model\UserStore();
		$where = [
			['user_id','=',$userId],
			['store_id','=',$storeId],
			['status','<>',2],
			['type','=',4],
		];
		return $modelUserStore->where($where)->value('id');
	}

	/**检查门店员工是否存在
	 */
	private function _checkShopEmployeeExist($userId,$storeId,$shopId){
		$modelUserShop = new \app\store\model\UserShop();
		$where = [
			['user_id','=',$userId],
			['store_id','=',$storeId],
			['shop_id','=',$shopId],
			['status','<>',2],
			['type','=',4],
		];
		return $modelUserShop->where($where)->value('id');
	}
}