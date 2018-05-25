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
	public function edit($factoryId =''){
		$validate = validate('Promotion');
		$data = input('post.');
		if(!$result = $validate->check($data)) {
			return errorMsg($validate->getError());
		}
		if(!empty($data['img'])){
			$data['img'] = moveImgFromTemp(config('upload_dir.factory_goods'),basename($data['img']));
		}
		$data['factory_id'] = $factoryId;
		$this ->startTrans();
		if(input('?post.promotion_id')){//修改
			$where = [
				['id','=',$data['promotion_id']],
				['factory_id','=',$factoryId],
			];
			$file = array(
				'img',
			);
			$oldInfo = $this -> getPromotion($where,$file);
			$data['update_time'] = time();
			$result = $this -> allowField(true) -> save($data,['id' => $data['promotion_id'],'factory_id'=>$factoryId]);
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
		$result = $modelGoods ->save(['sale_type_'.$data['storeType'] => 1],['id' => $data['goods_id'],'factory_id'=>$factoryId]);
		if(false === $result){
			$this ->rollback();
			return errorMsg($modelGoods->getLastSql());
		}
		$this ->commit();
		if(input('?post.promotion_id')){//修改成功后，删除旧图
			delImgFromPaths($oldInfo['img'],$data['img']);
		}
		return successMsg("成功！",['storeType'=>$data['storeType']]);
	}
	
	//分页查询
	public function pageQuery($_where=[],$join=[]){
		$where = [
			['p.status', '=', 0],
		];
		$activityStatus = (int)input('get.activityStatus');
		if($activityStatus == 1){//未结束
			$where[] = ['p.end_time','>',time()];
		}
		if($activityStatus == 0){//已结束
			$where[] = ['p.end_time','<=',time()];
		}

		$keyword = input('get.keyword','');
		if($keyword){
			$where[] = ['p.name', 'like', '%'.trim($keyword).'%'];
		}
		if(isset($_GET['storeType'])){//选择哪个类型店铺的促销活动
			$join = [
				['goods g','g.id = p.goods_id'],
				['goods_base gb','gb.id = g.goods_base_id'],
			];
			$where[] = ['g.sale_type','=',1];
		}
		$field = array(
			'p.id','p.name','p.img','p.goods_id','p.promotion_price','p.start_time','p.end_time','p.create_time','p.sort',
			'gb.name as goods_name','gb.retail_price'
		);
		$order = 'sort';
		$where = array_merge($_where, $where);
		$pageSize = (isset($_GET['pageSize']) && intval($_GET['pageSize'])) ?
			input('get.pageSize',0,'int') : config('custom.default_page_size');
		return $this->alias('p')->where($where)->join($join)->field($field)->order($order)->paginate($pageSize);
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
			'p.status' => 0,
		);
		$_join = array(
		);
		$where = array_merge($_where, $where);
		$_order = array(
			'p.id'=>'desc',
		);
		$order = array_merge($_order, $order);
		$list = $this->alias('g')
			->where($where)
			->field($field)
			->join(array_merge($_join,$join))
			->order($order)
			->limit($limit)
			->select();
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
	public function getInfo($where=[],$field=['*'],$join=[]){
		$_where = array(
			'p.status' => 0,
		);
		$_join = array(
		);
		$where = array_merge($_where, $where);
		$list = $this->alias('g')
			->where($where)
			->field($field)
			->join(array_merge($_join,$join))

			->find();
		if(!empty($list)){
			$list = $list->toArray();
		}
		return $list;
	}
}