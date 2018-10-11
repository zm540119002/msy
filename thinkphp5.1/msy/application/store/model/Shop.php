<?php
namespace app\store\model;

class Shop extends \common\model\Base{
	// 设置当前模型对应的完整数据表名称
	protected $table = 'shop';
	// 设置主键
	protected $pk = 'id';
	// 别名
	protected $alias = 's';
	// 设置当前模型的数据库连接
    protected $connection = 'db_config_store';

	/**编辑
	 */
	public function edit($userId,$factoryId,$storeId){
		if(!intval($userId) || !intval($factoryId) || !intval($storeId)){
			return errorMsg('参数错误');
		}
		$postData = input('post.');
		//数据验证
		$validateShop = new \app\store\validate\Shop();
		if(!$validateShop->scene('edit')->check($postData)){
			return errorMsg($validateShop->getError());
		}
		if(isset($postData['id']) && intval($postData['id'])){//修改
		}else{//新增
			$data = [
				'name' => trim($postData['shop_name']),
				'user_id' => $userId,
				'factory_id' => $factoryId,
				'store_id' => $storeId,
				'create_time' => time(),
			];
			$this->startTrans();//事务开启
			$res = $this->isUpdate(false)->save($data);
			if($res===false){
				$this->rollback();//事务回滚
				return errorMsg('失败',$this->getError());
			}
			$shopId = $this->getAttr('id');
			//验证用户是否存在
			$managerId = $this->checkUserByMobilePhone($postData['mobile_phone']);
			if(!$managerId){//不存在
				unset($postData['id']);
				$postData['type'] = 0;
				$postData['name'] = trim($postData['name']);
				$postData['nickname'] = $postData['name'];
				$postData['create_time'] = time();
				$modelUser = new \common\model\User();
				$res = $modelUser->isUpdate(false)->save($postData);
				if($res===false){
					$this->rollback();//事务回滚
					return errorMsg('失败',$this->getError());
				}
				$managerId = $modelUser->getAttr('id');
			}
			//验证用户是否为门店店长
			$userShopId = $this->checkManager($userId,$factoryId,$storeId,$shopId);
			if(!$userShopId){//不是管理员
				$postData['type'] = 3;
				$postData['user_id'] = $managerId;
				$postData['factory_id'] = $factoryId;
				$postData['store_id'] = $storeId;
				$postData['shop_id'] = $shopId;
				$modelUserShop = new \app\store\model\UserShop();
				$res = $modelUserShop->isUpdate(false)->save($postData);
				if($res===false){
					$this->rollback();//事务回滚
					return errorMsg('失败',$this->getError());
				}
				$userShopId = $modelUserShop->getAttr('id');
			}
			$this->commit();//事务提交
			$postData['id'] = $shopId;
			$postData['user_shop_id'] = $userShopId;
		}
		return successMsg('成功！',$postData);
	}

	/**验证用户是否为店长
	 */
	private function checkManager($userId,$factoryId,$storeId,$shopId){
		$modelUserShop = new \app\store\model\UserShop();
		$where = [
			['user_id','=',$userId],
			['factory_id','=',$factoryId],
			['store_id','=',$storeId],
			['shop_id','=',$shopId],
			['status','<>',2],
			['type','=',3],
		];
		return $modelUserShop->where($where)->value('id');
	}
}