<?php
namespace app\factory\model;
use GuzzleHttp\Psr7\Request;
use think\Model;
use think\Db;
/**
 * 基础模型器
 */

class Promotion extends Model {
	// 设置当前模型对应的完整数据表名称
	protected $table = 'promotion';
	// 设置当前模型的数据库连接
	protected $connection = 'db_config_factory';
	/**
	 * 新增和修改
	 */
	public function edit($factory_id =''){
		$validate = validate('Promotion');
		$data = input('post.');
		if(!$result = $validate->check($data)) {
			return errorMsg($validate->getError());
		}
		if(!empty($data['img'])){
			$data['img'] = moveImgFromTemp(config('upload_dir.factory_goods'),basename($data['img']));
		}
		$data['factory_id'] = $factory_id;
		$this ->startTrans();
		if(input('?post.promotion_id')){//修改
			$where = [
				['id','=',$data['promotion_id']],
				['factory_id','=',$factory_id],
			];
			$file = array(
				'img',
			);
			$oldInfo = $this -> getPromotion($where,$file);
			$data['update_time'] = time();
			$result = $this -> allowField(true) -> save($data,['id' => $data['promotion_id'],'factory_id'=>$factory_id]);
			if(false == $result){
				$this ->rollback();
				return errorMsg($this->getError());
			}
		}else{//新增
			$data['create_time'] = time();
			$result = $this -> allowField(true) -> save($data);
			if(false == $result){
				$this ->rollback();
				return errorMsg($this->getError());
			}
		}
		$modelGoods  = new \app\factory\model\Goods;
		$result = $modelGoods ->save([$data['storeType'] => 1],['id' => $data['goods_id']]);
		if(false === $result){
			$this ->rollback();
			return errorMsg($this->getError());
		}
		$this ->commit();
		if(input('?post.promotion_id')){//修改成功后，删除旧图
			delImgFromPaths($oldInfo['img'],$data['img']);
		}
		return successMsg("成功！");
	}
	
	//分页查询
	public function pageQuery(){
		$where = [
			['status', '=', 0],
		];
		$keyword = input('get.keyword','');
		if($keyword){
			$where[] = ['name', 'like', '%'.trim($keyword).'%'];
		}
		$field = array(
			'id','name','settle_price','retail_price','sale_price','thumb_img'
		);
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
	public function selectPromotion($where=[],$field=[],$order=[],$join=[],$limit=''){
		$_where = array(
			'p.status' => 0,
		);
		$_join = array(
		);
		$where = array_merge($_where, $where);
		$_order = array(
			'id'=>'desc',
		);
		$order = array_merge($_order, $order);
		if($field){
			$list = $this->alias('p')
				->where($where)
				->field($field)
				->join(array_merge($_join,$join))
				->order($order)
				->limit($limit)
				->select();
		}else{
			$list = $this->alias('p')
				->where($where)
				->join(array_merge($_join,$join))
				->order($order)
				->limit($limit)
				->select();
		}
		if(!empty($list)){
			return $list->toArray();
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
	public function getPromotion($where=[],$field=[],$join=[]){
		$_where = array(
			'p.status' => 0,
		);
		$where = array_merge($_where, $where);

		if($field){
			$info = $this->alias('p')
				->field($field)
				->join($join)
				->where($where)
				->find();
		}else{
			$info = $this->alias('p')
				->where($where)
				->join($join)
				->find();
		}
		if(!empty($info)){
			return $info->toArray();
		}
		return $info;
	}
}