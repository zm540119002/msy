<?php
namespace app\index_admin\model;
use think\Model;
use think\Db;
/**
 * 基础模型器
 */

class Store extends Model {
	// 设置当前模型对应的完整数据表名称
	protected $table = 'store';
	// 设置主键
	protected $pk = 'id';
	// 设置当前模型的数据库连接
    protected $connection = 'db_config_factory';
	/**
	 * 审核厂商
	 */
	public function audit(){
		$postData = input('post.');
		$res = $this->isUpdate(true)->save($postData);
		if($res===false){
			return errorMsg('更新失败',$this->getError());
		}else{
			return successMsg('成功！',$postData);
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
			's.status' => 0,
		);
		$_join = array(
		);
		$where = array_merge($_where, $where);
		$_order = array(
			's.id'=>'desc',
		);
		$order = array_merge($_order, $order);
		$list = $this->alias('s')
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
			's.status' => 0,
		);
		$where = array_merge($_where, $where);
		$_join = array(
		);
		$info = $this->alias('s')
			->field($field)
			->join(array_merge($_join,$join))
			->where($where)
			->find();
		return $info?$info->toArray():[];
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
	
	//获取店铺的列表
	public function getStoreList($factoryId=''){
		//企业旗舰店
		$where = [
			['s.factory_id','=',$factoryId],
			['s.store_type','=',1],
		];
		$file = ['s.id,s.store_type,s.run_type,s.auth_status,s.create_time,s.logo_img,s.update_time,s.is_default,f.name,r.logo_img as img'];
		$join =[
			['factory f','f.id = s.foreign_id'],
			['record r','s.foreign_id = r.factory_id'],
		];
		$factoryStore = $this -> getList($where,$file,$join);
		//品牌旗舰店
		$where = [
			['s.factory_id','=',$factoryId],
			['s.store_type','=',2],
		];
		$file = ['s.id,s.store_type,s.run_type,s.auth_status,s.create_time,s.update_time,s.logo_img,s.is_default,b.name,b.brand_img as img'];
		$join =[
			['brand b','b.id = s.foreign_id'],
		];
		$brandStores = $this->getList($where,$file,$join);
		$storeList = array_merge($factoryStore,$brandStores);
		return $storeList;
	}

	//获取单店铺的详情信息
	public function getStoreInfo($store){
		$where = [
			['s.id','=',$store['id']],
		];
		if($store['store_type'] == 1){
			$file = ['s.id,s.store_type,s.run_type,s.auth_status,s.create_time,s.update_time,s.logo_img,s.is_default,f.name,r.logo_img as img'];
			$join =[
				['factory f','f.id = s.foreign_id'],
				['record r','s.foreign_id = r.factory_id'],
			];
			$storeInfo = $this -> getInfo($where,$file,$join);
		}
		if($store['store_type'] == 2){
			$file = ['s.id,s.store_type,s.run_type,s.auth_status,s.create_time,s.update_time,s.logo_img,b.name,b.brand_img as img'];
			$join =[
				['brand b','b.id = s.foreign_id'],
			];
			$storeInfo = $this -> getInfo($where,$file,$join);
		}
		return $storeInfo;
	}

	/**检查店铺是否属于此厂商
	 */
	public function checkStoreExist($id,$factoryId){
		$where = [
			['id','=',$id],
			['factory_id','=',$factoryId]
		];
		$count = $this->where($where)->count();
		if($count){
			return true;
		}else{
			return false;
		}
	}

	/**
	 * 分页查询 商品
	 * @param array $_where
	 * @param array $_field
	 * @param array $_join
	 * @param string $_order
	 * @return \think\Paginator
	 */
	public function pageQuery($_where=[],$_field=['*'],$_join=[],$_order=[]){
		$where = [
			['s.status', '=', 0],
		];
		$keyword = input('get.keyword','');
		if($keyword){
			$where[] = ['s.name|s.name', 'like', '%'.trim($keyword).'%'];
		}
		if(input('?get.auth_status')){
			$authStatus = input('get.auth_status','int');
			$where[] = ['b.auth_status', '=',$authStatus];
		}
		$order = [
			's.auth_status'=>'asc',
			's.id'=>'desc'
		];
		$join = [];
		$where = array_merge($_where, $where);
		$order = array_merge($_order,$order);
		$join  = array_merge($_join,$join);
		$pageSize = (isset($_GET['pageSize']) && intval($_GET['pageSize'])) ?
			input('get.pageSize',0,'int') : config('custom.default_page_size');
		return $list = $this->alias('b')->join($join)->where($where)->field($_field)->order($order)->paginate($pageSize);
	}
}