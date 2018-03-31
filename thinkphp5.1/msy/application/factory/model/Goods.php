<?php
namespace app\factory\model;
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
	 * 新增
	 */
	public function add(){
		$validate = validate('Goods');
		$data = input('post.');
		if(!$result = $validate->scene('add')->check($data)) {
			return errorMsg($validate->getError());
		}
		$data['thumb_img'] = moveImgFromTemp(config('upload_dir.factory_goods'),basename($data['thumb_img']));
		$data['main_img']  = moveImgFromTemp(config('upload_dir.factory_goods'),basename($data['main_img']));
		$tempArr = array();
		foreach ($data['details_img'] as $item) {
			if($item){
				$tempArr[] = moveImgFromTemp(config('upload_dir.factory_goods'),basename($item));
			}
		}
		$data['details_img'] = implode(',',$tempArr);
		$data['create_time'] = time();
		$result = $this -> allowField(true) -> save($data);
		if(false !== $result){
			return successMsg("添加成功！");
		}else{
			return errorMsg($this->getError());
		}
	}

	/**
	 * 修改
	 */
	public function edit(){
		$data = input('post.');
		$validate = validate('Goods');
		if(!$result = $validate->scene('edit')->check($data)) {
			return errorMsg($validate->getError());
		}
		$data['Goods_img'] = moveImgFromTemp(config('upload_dir.factory_goods'),basename($data['Goods_img']));
		$data['certificate'] = moveImgFromTemp(config('upload_dir.factory_goods'),basename($data['certificate']));
		$data['authorization'] = moveImgFromTemp(config('upload_dir.factory_goods'),basename($data['authorization']));
		$data['update_time'] = time();
		$result = $this->allowField(true)->save($data);
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
	public function selectGoods($where=[],$field=[],$order=[],$join=[],$limit=''){
		$_where = array(
			'b.status' => 0,
		);
		$_join = array(
		);
		$where = array_merge($_where, $where);
		if($field){
			$list = $this->alias('s')
				->where($where)
				->field($field)
				->join(array_merge($_join,$join))
				->order($order)
				->limit($limit)
				->select();
		}else{
			$list = $this->alias('s')
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
	public function getGoods($where=[],$field=[],$join=[]){
		$_where = array(
			'status' => 0,
		);
		$where = array_merge($_where, $where);
		$_join = array(
		);
		if($field){
			$info = $this
				->field($field)
				->join(array_merge($_join,$join))
				->where($where)
				->find();
		}else{
			$info = $this
				->where($where)
				->join(array_merge($_join,$join))
				->find();
		}
		return $info;
	}
}