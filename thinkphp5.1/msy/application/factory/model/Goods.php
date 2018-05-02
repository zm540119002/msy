<?php
namespace app\factory\model;
use GuzzleHttp\Psr7\Request;
use think\Model;
use think\Db;
/**
 * 基础模型器
 */

class Goods extends Model {
	// 设置当前模型对应的完整数据表名称
	protected $table = 'goods';
	// 设置当前模型的数据库连接
	protected $connection = 'db_config_factory';

	/**
	 * 修改
	 */
	public function edit($goodsBaseId =''){
		$data = input('post.');
		$validate = validate('Goods');
		// if(!$result = $validate->scene('edit')->check($data)) {
		// 	return errorMsg($validate->getError());
		// }

		if(input('?post.goods_base_id')){//修改
			$data['update_time'] = time();
			$result = $this->allowField(true)->save($data, ['id' => $data['goods_id']]);
		}else{
			//批量添加
			$list = $data['goodsExtend'];
			foreach ($list as $k=>$value){
				$list[$k]['goods_base_id'] = $goodsBaseId;
				$list[$k]['create_time'] = time();
			}
		}
		$result = $this->saveAll($list);
		if(false !== $result){
			return successMsg("成功");
		}else{
			return errorMsg('失败');
		}
	}

	//分页查询 商品
	public function pageQuery($_where = []){
		$where = [
			['status', '=', 0],
		];
		$keyword = input('get.keyword','');
		if($keyword){
			$where[] = ['name', 'like', '%'.trim($keyword).'%'];
		}
		$field =['id','name','retail_price','thumb_img',];
		$_field = [];
		if(isset($_GET['storeType'])){
			if($_GET['storeType'] == 'purchases'){
				$where[] = ['purchases_store','=',1];
				$_field = ['purchases_shelf','settle_price_purchases','sale_price_purchases'];
			}
			if($_GET['storeType'] == 'commission'){
				$where[] =  ['commission_store','=',1];
				$_field = ['commission_shelf','settle_price_commission','sale_price_commission'];
			}
			if($_GET['storeType'] == 'retail'){
				$where[] =  ['retail_store','=',1];
				$_field = ['retail_shelf','settle_price_retail','sale_price_retail'];
			}
		}
		$where = array_merge($_where, $where);
		$field = array_merge($_field,$field);
		$order = 'id';
		$pageSize = (isset($_GET['pageSize']) && intval($_GET['pageSize'])) ?
			input('get.pageSize',0,'int') : config('custom.default_page_size');
		return $this->where($where)->field($field)->order($order)->paginate($pageSize);
		
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
	public function selectGoods($where=[],$field=[],$join=[],$order=[],$limit=''){
		$_where = array(
			'g.status' => 0,
		);
		$_join = array(
		);
		$where = array_merge($_where, $where);
		$_order = array(
			'g.id'=>'desc',
		);
		$order = array_merge($_order, $order);
		if($field){
			$list = $this->alias('g')
				->where($where)
				->field($field)
				->join(array_merge($_join,$join))
				->order($order)
				->limit($limit)
				->select();
		}else{
			$list = $this->alias('g')
				->where($where)
				->join(array_merge($_join,$join))
				->order($order)
				->limit($limit)
				->select();
		}
		if(!empty($list)){
			$list = $list->toArray();
		}
		return $list;
	}

	/**
	 * @param array $where
	 * @param array $field
	 * @param array $join
	 * @return array|null|\PDOStatement|string|Model
	 * 查找一条数据
	 */
	public function getGoods($where=[],$field=[],$join=[]){
		$_where = array(
			'g.status' => 0,
		);
		$where = array_merge($_where, $where);
		$_join = array(
		);
		if($field){
			$info = $this->alias('g')
				->field($field)
				->join(array_merge($_join,$join))
				->where($where)
				->find();
		}else{
			$info = $this->alias('g')
				->where($where)
				->join(array_merge($_join,$join))
				->find();
		}
		return $info;
	}
}