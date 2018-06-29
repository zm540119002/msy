<?php
namespace app\store\model;
use GuzzleHttp\Psr7\Request;
use think\Model;
use think\Db;
/**
 * 基础模型器
 */

class Goods extends Model {
	// 设置当前模型对应的完整数据表名称
	protected $table = 'goods';
	// 设置主键
	protected $pk = 'id';
	// 设置当前模型的数据库连接
	protected $connection = 'db_config_store';


	/**
	 * 编辑 新增和修改
	 * @param string $shopId
	 * @return array
	 */
	public function edit($shopId =''){
		$data = input('post.');
		if($this->_isExistGoodsName($data,$shopId)) {
			return errorMsg('本店已存在此商品名，请更改别的商品名');
		}
		$validate = validate('Goods');
		if(!$result = $validate ->check($data)) {
			return errorMsg($validate->getError());
		}
		if(!empty($data['thumb_img'])){
			$data['thumb_img'] = moveImgFromTemp(config('upload_dir.factory_goods'),basename($data['thumb_img']));
		}
		if(!empty($data['main_img'])){
			$mainImg =[];
			$tempMainImg = explode(",",$data['main_img']);
			array_pop($tempMainImg);
			foreach ($tempMainImg as $item) {
				if($item){
					$mainImg[] = moveImgFromTemp(config('upload_dir.factory_goods'),basename($item));
				}
			}
			$data['main_img'] = implode(",", $mainImg).',';
		}
		if(!empty($data['goods_video'])){
			$data['goods_video'] = moveImgFromTemp(config('upload_dir.factory_goods'),basename($data['goods_video']));
		}
		if(!empty($data['details_img'])){
			$detailsImg = [];
			$tempArray = explode(",",$data['details_img']);
			array_pop($tempArray);
			foreach ($tempArray as $item) {
				if($item){
					$detailsImg[] = moveImgFromTemp(config('upload_dir.factory_goods'),basename($item));
				}
			}
			$data['details_img'] = implode(",", $detailsImg).',';
		}
		if(input('?post.id')){//修改
			$where = [
				['id','=',$data['id']],
			];
			$file = array(
				'thumb_img','main_img','details_img','goods_video'
			);
			$oldGoodsInfo = $this -> getInfo($where,$file);
			if(empty($oldGoodsInfo)){
				return errorMsg('没有数据');
			}
			$data['update_time'] = time();
			$result = $this->allowField(true)->save($data, ['id' => $data['id'],'store_id'=>$shopId]);
		}else{
			$data['create_time'] = time();
			$data['store_id'] = $shopId;
			$result = $this -> allowField(true) -> save($data);
			if(!$result){
				return errorMsg('失败');
			}

		}
		if(false !== $result){
			if(input('?post.id')){//删除旧图片
				delImgFromPaths($oldGoodsInfo['thumb_img'],$data['thumb_img']);
				//删除就图片
				$oldMainImg = explode(",",$oldGoodsInfo['main_img']);
				array_pop($oldMainImg);
				$newMainImg = explode(",",$data['main_img']);
				array_pop($newMainImg);
				delImgFromPaths($oldMainImg,$newMainImg);

				$oldDetailsImg = explode(",",$oldGoodsInfo['details_img']);
				array_pop($oldDetailsImg);
				$newDetailsImg = explode(",",$data['details_img']);
				array_pop($newDetailsImg);
				delImgFromPaths($oldDetailsImg,$newDetailsImg);

				delImgFromPaths($oldGoodsInfo['goods_video'],$data['goods_video']);
			}
			return successMsg("成功");
		}else{
			return errorMsg('失败');
		}
	}

	//检查本店的商品是否同名,
	private function _isExistGoodsName($data,$shopId){
		$name = $data['name'];
		$where = [
			['store_id','=',$shopId],
			['name','=',$name],
		];
		if(isset($data['id']) && (int)$data['id']){//
			$id = $data['id'];
			$where[] =  ['id','<>',$id];
		}
		return $this->where($where)->count() ? true : false;
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
			'g.status' => 0,
		);
		$_join = array(
		);
		$where = array_merge($_where, $where);
		$_order = array(
			'g.id'=>'desc',
		);
		$order = array_merge($_order, $order);
		$list = $this->alias('g')
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
			'g.status' => 0,
		);
		$where = array_merge($_where, $where);
		$_join = array(
		);
		$info = $this->alias('g')
			->field($field)
			->join(array_merge($_join,$join))
			->where($where)
			->find();
		return $info?$info->toArray():[];
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
			['g.status', '=', 0],
		];
		$keyword = input('get.keyword','');
		if($keyword){
			$where[] = ['name', 'like', '%'.trim($keyword).'%'];
		}
		$order = [
			'sort'=>'desc',
			'line_num'=>'asc',
			'id'=>'desc'
		];
		$where = array_merge($_where, $where);
		$order = array_merge($_order,$order);
		$pageSize = (isset($_GET['pageSize']) && intval($_GET['pageSize'])) ?
			input('get.pageSize',0,'int') : config('custom.default_page_size');
		return $this->alias('g')->join($_join)->where($where)->field($_field)->order($order)->paginate($pageSize);
	}
	
	//设置库存
	public function setInventory($shopId=''){
		$data = input('post.');
		if(empty($data['id'] || !(int)$data['id'])){
			return errorMsg("参数错误");
		}
		$where = [
			['id','=',(int)$data['id']],
			['shop_id','=',$shopId],
		];
		$result = $this->where($where)->setInc('inventory',(int)$data['num'] );
		if(false !== $result){
			return successMsg("成功");
		}else{
			return errorMsg("失败");
		}
	}

}