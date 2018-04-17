<?php
namespace app\factory\model;
use think\Model;
use think\Db;
/**
 * 基础模型器
 */

class Brand extends Model {
	// 设置当前模型对应的完整数据表名称
	protected $table = 'brand';
//	// 设置当前模型的数据库连接
	protected $connection = 'db_config_factory';
	/**
	 * 新增
	 */
	public function add(){
		$data = input('post.');
		$validate = validate('Brand');
		if(!$result = $validate->scene('add')->check($data)) {
			return errorMsg($validate->getError());
		}
		$data['brand_img'] = moveImgFromTemp(config('upload_dir.factory_brand'),basename($data['brand_img']));
		$data['certificate'] = moveImgFromTemp(config('upload_dir.factory_brand'),basename($data['certificate']));
		$data['authorization'] = moveImgFromTemp(config('upload_dir.factory_brand'),basename($data['authorization']));
		$data['create_time'] = time();
		$result = $this->allowField(true)->save($data);
		if(false !== $result){
			return successMsg("已提交申请");
		}else{
			return errorMsg($this->getError());
		}
	}

	/**
	 * 修改
	 */
	public function edit(){
		$data = input('post.');
		$validate = validate('Brand');
		if(!$result = $validate->scene('edit')->check($data)) {
			return errorMsg($validate->getError());
		}
		$data['brand_img'] = moveImgFromTemp(config('upload_dir.factory_brand'),basename($data['brand_img']));
		$data['certificate'] = moveImgFromTemp(config('upload_dir.factory_brand'),basename($data['certificate']));
		$data['authorization'] = moveImgFromTemp(config('upload_dir.factory_brand'),basename($data['authorization']));
		$data['update_time'] = time();
		$result = $this->allowField(true)->save($data, ['id' => $data['brand_id']]);
		if(false !== $result){
			return successMsg("已提交申请");
		}else{
			return errorMsg($this->getError());
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
	public function selectBrand($where=[],$field=[],$order=[],$join=[],$limit=''){
		$_where = array(
			'b.status' => 0,
		);
		$_join = array(
		);
		$where = array_merge($_where, $where);
		if($field){
			$list = $this->alias('b')
				->where($where)
				->field($field)
				->join(array_merge($_join,$join))
				->order($order)
				->limit($limit)
				->select();
		}else{
			$list = $this->alias('b')
				->where($where)
				->join(array_merge($_join,$join))
				->order($order)
				->limit($limit)
				->select();
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
	public function getBrand($where=[],$field=[],$join=[]){
		$_where = array(
			'b.status' => 0,
		);
		$_join = array(
		);
		$where = array_merge($_where, $where);
		if($field){
			$info = $this->alias('b')
				->field($field)
				->where($where)
				->join(array_merge($_join,$join))
				->find();
		}else{
			$info = $this->alias('b')
				->where($where)
				->join(array_merge($_join,$join))
				->find();
		}
		return $info;
	}
}