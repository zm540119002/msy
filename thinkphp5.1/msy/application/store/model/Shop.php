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
		$postData['mobile_phone'] = trim($postData['mobile_phone']);
		$postData['name'] = trim($postData['name']);
		$postData['shop_name'] = trim($postData['shop_name']);
		//验证用户是否存在
		$managerId = $this->checkUserExistByMobilePhone($postData['mobile_phone']);
		$this->startTrans();//事务开启
		if(!$managerId){//不存在
			$saveData = [
				'name' => $postData['name'],
				'type' => 0,
				'factory_id' => $factoryId,
				'mobile_phone' => $postData['mobile_phone'],
				'create_time' => time(),
			];
			$modelUser = new \common\model\User();
			$res = $modelUser->isUpdate(false)->save($saveData);
			if($res===false){
				$this->rollback();//事务回滚
				return errorMsg('失败',$this->getError());
			}
			$managerId = $modelUser->getAttr('id');
		}
		if(isset($postData['shopId']) && intval($postData['shopId'])
			&& isset($postData['userShopId']) && intval($postData['userShopId'])){//修改
			$saveData = [
				'name' => $postData['shop_name'],
				'update_time' => time(),
			];
			$where = [
				['factory_id','=',$factoryId],
				['store_id','=',$storeId],
				['id','=',$postData['shopId']],
				['status','=',0],
			];
			$res = $this->isUpdate(true)->save($saveData,$where);
			if($res===false){
				$this->rollback();//事务回滚
				return errorMsg('失败',$this->getError());
			}
			$where = [
				['factory_id','=',$factoryId],
				['store_id','=',$storeId],
				['shop_id','=',$postData['shopId']],
				['id','=',$postData['userShopId']],
				['status','=',0],
				['type','=',3],
			];

			$saveData = [
				'user_id' => $managerId,
				'user_name' => $postData['name'],
			];
			$modelUserShop = new \app\store\model\UserShop();
			$res = $modelUserShop->isUpdate(true)->save($saveData,$where);
			if($res===false){
				$this->rollback();//事务回滚
				return errorMsg('失败',$modelUserShop->getError());
			}
		}else{//新增
			//保存门店信息
			$saveData = [
				'name' => $postData['shop_name'],
				'user_id' => $userId,
				'factory_id' => $factoryId,
				'store_id' => $storeId,
				'create_time' => time(),
			];
			//数据验证
			$validateShop = new \app\store\validate\Shop();
			if(!$validateShop->scene('edit')->check($saveData)){
				return errorMsg($validateShop->getError());
			}
			$res = $this->isUpdate(false)->save($saveData);
			if($res===false){
				$this->rollback();//事务回滚
				return errorMsg('失败',$this->getError());
			}
			$shopId = $this->getAttr('id');

			$modelUserShop = new \app\store\model\UserShop();
			//设置门店拥有者和门店店长
			$saveData = [
				[
					'type' => 1,
					'user_id' => $userId,
					'factory_id' => $factoryId,
					'store_id' => $storeId,
					'shop_id' => $shopId,
				],[
					'type' => 3,
					'user_id' => $managerId,
					'user_name' => $postData['name'],
					'factory_id' => $factoryId,
					'store_id' => $storeId,
					'shop_id' => $shopId,
				],
			];
			$res = $modelUserShop->isUpdate(false)->saveAll($saveData);
			if($res===false){
				$this->rollback();//事务回滚
				return errorMsg('失败',$this->getError());
			}
			$userShopId = $res[1]['id'];
			$postData['shop_id'] = $shopId;
			$postData['user_shop_id'] = $userShopId;
		}
		$this->commit();//事务提交
		return successMsg('成功！',$postData);
	}
}