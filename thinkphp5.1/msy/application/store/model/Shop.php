<?php
namespace app\store\model;
use think\Model;
use think\Db;
/**
 * 基础模型器
 */

class Shop extends Model {
	// 设置当前模型对应的完整数据表名称
	protected $table = 'shop';
	// 设置主键
	protected $pk = 'id';
	// 设置当前模型的数据库连接
    protected $connection = 'db_config_store';
//	protected $readonly = ['name'];

	/**
	 * 编辑
	 */
	public function edit($storeId=''){
		$data = input('post.');
		$data['store_id'] = $storeId;
		$validate = validate('Store');
//		if(!$result = $validate->check($data)) {
//			return errorMsg($validate->getError());
//		}
		if(input('?post.id')){
			$data['update_time'] = time();
			$result = $this->allowField(true)->save($data,['id' => $data['store_id'],'store_id'=>$storeId]);
			if(false !== $result){
				return successMsg("成功");
			}
			return errorMsg('失败',$this->getError());
		}else{
			$data['create_time'] = time();
			$result = $this->allowField(true)->save($data);
			if(!$result){
				$this ->rollback();
				return errorMsg('失败');
			}
			return successMsg('提交申请成功');
		}
	}
	
	/**
	 * @param array $where
	 * @param array $field
	 * @param array $order
	 * @param array $join
	 * @param string $limit
	 * @return array|\PDOStatement|string|\think\Collection
	 * 查询多条数据
	 */
	public function getList($where=[],$field=['*'],$join=[],$order=[],$limit=''){
		$_where = array(
			'sh.status' => 0,
		);
		$_join = array(
		);
		$where = array_merge($_where, $where);
		$_order = array(
			'sh.id'=>'desc',
		);
		$order = array_merge($_order, $order);
		$list = $this->alias('sh')
			->where($where)
			->field($field)
			->join(array_merge($_join,$join))
			->order($order)
			->limit($limit)
			->select();
	    return count($list)?$list->toArray():[];
	}

	/**
	 * @param array $where
	 * @param array $field
	 * @param array $join
	 * @return array|null|\PDOStatement|string|Model
	 * 查找一条数据
	 */
	public function getInfo($where=[],$field=['*'],$join=[]){
		$_where = array(
			'sh.status' => 0,
		);
		$where = array_merge($_where, $where);
		$_join = array(
		);
		$info = $this->alias('sh')
			->field($field)
			->join(array_merge($_join,$join))
			->where($where)
			->find();
		return $info?$info->toArray():[];
	}

	//设置默认店铺
	public function setDefaultShop($storeId=''){
		if(request()->isAjax()){
			$id = (int)input('post.id');
			if(!$id){
				return errorMsg('参数错误');
			}
			$this->startTrans();
			$data = array('is_default' => 1);
			$result = $this->allowField(true)->save($data,['id' => $id,'store_id'=>$storeId]);
			if(false === $result){
				$this->rollback();
				return errorMsg('修改默认失败');
			}
			$where = [
				['id','<>',$id],
				['store_id','=',$storeId],
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
	
	//获取店铺的列表
	public function getShopList($storeId=''){
		//企业旗舰店
		$where = [
			['sh.store_id','=',$storeId],
			['sh.shop_type','=',1],
		];
		$file = ['sh.id,sh.shop_type,sh.run_type,sh.auth_status,sh.create_time,sh.update_time,sh.is_default,s.name,r.logo_img as img'];
		$join =[
			['store s','s.id = sh.foreign_id'],
			['record r','sh.foreign_id = r.store_id'],
		];
		$factoryShop = $this -> getList($where,$file,$join);
		//品牌旗舰店
		$where = [
			['sh.store_id','=',$storeId],
			['sh.shop_type','=',2],
		];
		$file = ['sh.id,sh.shop_type,sh.run_type,sh.auth_status,sh.create_time,sh.update_time,sh.is_default,b.name,b.brand_img as img'];
		$join =[
			['brand b','b.id = sh.foreign_id'],
		];
		$brandShops = $this->getList($where,$file,$join);
		$shopList = array_merge($factoryShop,$brandShops);
		return $shopList;
	}

	//获取单店铺的详情信息
	public function getShopInfo($store){
		$where = [
			['sh.id','=',$store['id']],
		];
		if($store['shop_type'] == 1){
			$file = ['sh.id,sh.shop_type,sh.run_type,sh.auth_status,sh.create_time,sh.update_time,sh.is_default,s.name,r.logo_img as img'];
			$join =[
				['store s','s.id = sh.foreign_id'],
				['record r','sh.foreign_id = r.store_id'],
			];
			$shopInfo = $this -> getInfo($where,$file,$join);
		}
		if($store['shop_type'] == 2){
			$file = ['sh.id,sh.shop_type,sh.run_type,sh.auth_status,sh.create_time,sh.update_time,b.name,b.brand_img as img'];
			$join =[
				['brand b','b.id = sh.foreign_id'],
			];
			$shopInfo = $this -> getInfo($where,$file,$join);
		}
		return $shopInfo;
	}

	/**检查店铺是否属于此厂商
	 */
	public function checkShopExist($id,$storeId){
		$where = [
			['id','=',$id],
			['store_id','=',$storeId]
		];
		$count = $this->where($where)->count();
		if($count){
			return true;
		}else{
			return false;
		}

	}
}