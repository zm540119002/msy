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
	public function edit($goodsBaseId ='',$storeId =''){
		$data = input('post.');
		$validate = validate('Goods');
		// if(!$result = $validate->scene('edit')->check($data)) {
		// 	return errorMsg($validate->getError());
		// }

		if(input('?post.goods_base_id')){
			$data['update_time'] = time();
			$result = $this->allowField(true)->save($data,['goods_base_id' => $goodsBaseId,'store_id'=>$storeId]);
			if(false !== $result){
				return successMsg("成功");
			}
			return errorMsg('失败',$this->getError());
		}else{
			$data['create_time'] = time();
			$data['store_id'] = $storeId;
			$result = $this->allowField(true)->save($data);
			if(!$result){
				$this ->rollback();
				return errorMsg('失败');
			}
			return successMsg('提交申请成功');
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
	public function pageQuery($_where=[],$_field=[],$_join=[],$_order=''){
		$where = [
			['g.status', '=', 0],
		];

		$keyword = input('get.keyword','');
		if($keyword){
			$where[] = ['name', 'like', '%'.trim($keyword).'%'];
		}
		$field = [
			'g.goods_base_id,g.id,g.sale_price,g.sale_type,g.shelf_status,g.create_time,g.update_time,g.store_type,
                gb.name,gb.retail_price,gb.trait,gb.cat_id_1,gb.cat_id_2,gb.cat_id_3,
                gb.thumb_img,gb.goods_video,gb.main_img,gb.details_img,gb.tag,gb.create_time,gb.update_time,
                gb.parameters'
		];
		if(input('?get.storeType') && (int)input('get.storeType')){
			$where[] = ['store_type','=',(int)input('get.storeType')];
		}
		$join =[
			['goods_base gb','gb.id = g.goods_base_id'],
		];
		$where = array_merge($_where, $where);
		$field = array_merge($_field,$field);
		$join = array_merge($_join,$join);
		$order = 'id';
		$pageSize = (isset($_GET['pageSize']) && intval($_GET['pageSize'])) ?
			input('get.pageSize',0,'int') : config('custom.default_page_size');
		 return $this->alias('g')->join($join)->where($where)->field($field)->order($order)->paginate($pageSize);
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