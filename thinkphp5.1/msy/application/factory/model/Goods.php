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
	 * 新增
	 */
	public function add($factory_id =''){
		$validate = validate('Goods');
		$data = input('post.');
//		if(!$result = $validate->scene('add')->check($data)) {
//			return errorMsg($validate->getError());
//		}
		if(!empty($data['thumb_img'])){
			$data['thumb_img'] = moveImgFromTemp(config('upload_dir.factory_goods'),basename($data['thumb_img']));
		}
		if(!empty($data['main_img'])){
			$mainImg = '';
			$tempMainImg = explode(",",$data['main_img']);
			array_pop($tempMainImg);
			foreach ($tempMainImg as $item) {
				if($item){
					$mainImg = moveImgFromTemp(config('upload_dir.factory_goods'),basename($item)).','.$mainImg;
				}
			}
			$data['main_img'] = $mainImg;
		}
		if(!empty($data['goods_video'])){
			$goodsVideo = '';
			$tempGoodsVideo = explode(",",$data['goods_video']);
			array_pop($tempGoodsVideo);
			foreach ($tempGoodsVideo as $item) {
				if($item){
					$goodsVideo = moveImgFromTemp(config('upload_dir.factory_goods'),basename($item)).','.$goodsVideo;
				}
			}
			$data['goods_video'] = $goodsVideo;
		}
		if(!empty($data['details_img'])){
			$detailsImg = '';
			$tempArray = explode(",",$data['details_img']);
			array_pop($tempArray);
			foreach ($tempArray as $item) {
				if($item){
					$detailsImg = moveImgFromTemp(config('upload_dir.factory_goods'),basename($item)).','.$detailsImg;
				}
			}
			$data['details_img'] = $detailsImg;
		}
		$data['factory_id'] = $factory_id;
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
	public function edit($factory_id =''){
		$data = input('post.');
		$validate = validate('Goods');
		// if(!$result = $validate->scene('edit')->check($data)) {
		// 	return errorMsg($validate->getError());
		// }
		$where = [
			['id','=',$data['goods_id']],
			['factory_id','=',$factory_id],
		];
		$file = array(
			'thumb_img','main_img','details_img','goods_video'
		);
		$oldGoodsInfo = $this -> getGoods($where,$file);
		if(!empty($data['thumb_img'])){
			$data['thumb_img'] = moveImgFromTemp(config('upload_dir.factory_goods'),basename($data['thumb_img']));
		}
		if(!empty($data['main_img'])){
			$mainImg = '';
			$tempMainImg = explode(",",$data['main_img']);
			array_pop($tempMainImg);
			foreach ($tempMainImg as $item) {
				if($item){
					$mainImg = moveImgFromTemp(config('upload_dir.factory_goods'),basename($item)).','.$mainImg;
				}
			}
			$data['main_img'] = $mainImg;
		}
		if(!empty($data['goods_video'])){
			$goodsVideo = '';
			$tempGoodsVideo = explode(",",$data['goods_video']);
			array_pop($tempGoodsVideo);
			foreach ($tempGoodsVideo as $item) {
				if($item){
					$goodsVideo = moveImgFromTemp(config('upload_dir.factory_goods'),basename($item)).','.$goodsVideo;
				}
			}
			$data['goods_video'] = $goodsVideo;
		}
		if(!empty($data['details_img'])){
			$detailsImg = '';
			$tempArray = explode(",",$data['details_img']);
			array_pop($tempArray);
			foreach ($tempArray as $item) {
				if($item){
					$detailsImg = moveImgFromTemp(config('upload_dir.factory_goods'),basename($item)).','.$detailsImg;
				}
			}
			$data['details_img'] = $detailsImg;
		}
		$data['update_time'] = time();
		$result = $this->allowField(true)->save($data, ['id' => $data['goods_id']]);
		if(false !== $result){
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

			$oldGoodsVideo = explode(",",$oldGoodsInfo['goods_video']);
			array_pop($oldGoodsVideo);
			$newGoodsVideo = explode(",",$data['goods_video']);
			array_pop($newGoodsVideo);
			delImgFromPaths($oldGoodsVideo,$newGoodsVideo);
			return successMsg("修改成功");
		}else{
			return errorMsg($this->getError());
		}
	}

	//分页查询
	public function pageQuery($_where = []){
		$where = [
			['status', '=', 0],
		];
		$keyword = input('get.keyword','');
		if($keyword){
			$where[] = ['name', 'like', '%'.trim($keyword).'%'];
		}
		$field =['id','name','settle_price','retail_price','sale_price','thumb_img',];
		if(isset($_GET['storeType'])){
			if($_GET['storeType'] == 'purchases_store'){
				$where[] = ['purchases_store','=',1];
				$field[] = 'purchases_shelf';
			}
			if($_GET['storeType'] == 'commission_store'){
				$where[] =  ['commission_store','=',1];
				$field[] = 'commission_shelf';
			}
			if($_GET['storeType'] == 'retail_store'){
				$where[] =  ['retail_store','=',1];
				$field[] = 'retail_shelf';
			}
		}
		$where = array_merge($_where, $where);
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
	public function selectGoods($where=[],$field=[],$order=[],$join=[],$limit=''){
		$_where = array(
			'g.status' => 0,
		);
		$_join = array(
		);
		$where = array_merge($_where, $where);
		$_order = array(
			'id'=>'desc',
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