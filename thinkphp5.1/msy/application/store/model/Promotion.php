<?php
namespace app\store\model;
use GuzzleHttp\Psr7\Request;
use think\Model;
use think\Db;
/**
 * 基础模型器
 */

class Promotion extends Model {
	// 设置当前模型对应的完整数据表名称
	protected $table = 'promotion';
	// 设置主键
	protected $pk = 'id';
	// 设置当前模型的数据库连接
	protected $connection = 'db_config_store';
	/**
	 * 新增和修改
	 */
	public function edit($shopId =''){
		$validate = validate('Promotion');
		$data = input('post.');
		if(!$result = $validate->check($data)) {
			return errorMsg($validate->getError());
		}
		if(!empty($data['img'])){
			$data['img'] = moveImgFromTemp(config('upload_dir.store_goods'),basename($data['img']));
		}
		$data['shop_id'] = $shopId;
		$this ->startTrans();
		if(input('?post.id')){//修改
			$where = [
				['id','=',$data['id']],
				['shop_id','=',$shopId],
			];
			$file = array(
				'img,goods_id',
			);
			$oldInfo = $this -> getInfo($where,$file);
			$data['update_time'] = time();
			$result = $this -> allowField(true) -> save($data,['id' => $data['id'],'shop_id'=>$shopId]);
			if(false == $result){
				$this ->rollback();
				return errorMsg('失败');
			}
		}else{//新增
			$data['create_time'] = time();
			$result = $this -> allowField(true) -> save($data);
			if(false == $result){
				$this ->rollback();
				return errorMsg('失败');
			}
		}
		$modelGoods  = new \app\store\model\Goods;
		$result = $modelGoods ->save(['sale_type'=>1],['id' => $data['goods_id'],'shop_id'=>$shopId]);
		if(false === $result){
			$this ->rollback();
			return errorMsg('失败');
		}
		if(!empty($oldInfo) && $oldInfo['goods_id'] != $data['goods_id']){ //如换商品，把旧商品销售类型改为普通商品类型
			$result = $modelGoods ->save(['sale_type'=>0],['id' => $oldInfo['goods_id'],'shop_id'=>$shopId]);
			if(false === $result){
				$this ->rollback();
				return errorMsg('失败');
			}
		}
		$this ->commit();
		if(input('?post.id')){//修改成功后，删除旧图
			delImgFromPaths($oldInfo['img'],$data['img']);
		}
		return successMsg("成功");
	}

	/**
	 * 删除
	 */
	public function del($shopId,$tag = true){
		$data = input('post.');
		$where = [
			['shop_id','=',$shopId]
		];
		if(is_array($data['id'])){
			$where[] = ['id','in',$data['id']];
		}else{
			$where[] = ['id','=',$data['id']];
		}
		if($tag){//标记删除
			$result = $this->where($where)->setField('status',2);
		}else{
			$result = $this->where($where)->delete();
		}
		if(false !== $result){
			return successMsg("已删除");
		}
		return errorMsg('失败');
	}
	
	//分页查询
	public function pageQuery($_where=[],$field=['*'],$join=[],$_order=[]){
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
		$order = ['id'=>'desc','sort'=>'desc'];
		$where = array_merge($_where, $where);
		$order = array_merge($_order, $order);
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
			'p.status' => 0,
		);
		$_join = array(
		);
		$where = array_merge($_where, $where);
		$info = $this->alias('p')
			->where($where)
			->field($field)
			->join(array_merge($_join,$join))
			->find();
		return $info?$info->toArray():[];
	}
}