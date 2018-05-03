<?php
namespace app\factory\model;
use GuzzleHttp\Psr7\Request;
use think\Model;
use think\Db;
/**
 * 基础模型器
 */

class GoodsBase extends Model {
	// 设置当前模型对应的完整数据表名称
	protected $table = 'goods_base';
	// 设置当前模型的数据库连接
	protected $connection = 'db_config_factory';

	/**
	 * 编辑 新增和修改
	 * @param string $factory_id
	 * @return array
	 */
	public function edit($factoryId =''){
		$data = input('post.');
		$validate = validate('GoodsBase');
		// if(!$result = $validate->scene('edit')->check($data)) {
		// 	return errorMsg($validate->getError());
		// }
		$where = [
			['id','=',$data['goods_base_id']],
			['factory_id','=',$factoryId],
		];
		$file = array(
			'thumb_img','main_img','details_img','goods_video'
		);
		if(input('?post.goods_base_id')){//修改，查询
			$oldGoodsInfo = $this -> getGoodsBase($where,$file);
			if(empty($oldGoodsInfo)){
				return errorMsg('没有数据');
			}
		}
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
		if(input('?post.goods_base_id')){//修改
			$data['update_time'] = time();
			$result = $this->allowField(true)->save($data, ['id' => $data['goods_base_id']]);
			$goodsBaseId = $data['goods_base_id'];
		}else{
			$data['factory_id'] = $factoryId;
			$data['create_time'] = time();
			$result = $this -> allowField(true) -> save($data);
			$goodsBaseId =  $this->getAttr('id');
			if(!$result){
				$this -> rollback();
				return errorMsg('失败');
			}

		}
		if(false !== $result){
			if(input('?post.goods_base_id')){//删除旧图片
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
			}
			return successMsg("成功",array('id'=>$goodsBaseId));
		}else{
			return errorMsg('失败');
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
	public function selectGoodsBase($where=[],$field=[],$join=[],$order=[],$limit=''){
		$_where = array(
			'gb.status' => 0,
		);
		$_join = array(
		);
		$where = array_merge($_where, $where);
		$_order = array(
			'gb.id'=>'desc',
		);
		$order = array_merge($_order, $order);
		if($field){
			$list = $this->alias('gb')
				->where($where)
				->field($field)
				->join(array_merge($_join,$join))
				->order($order)
				->limit($limit)
				->select();
		}else{
			$list = $this->alias('gb')
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
	public function getGoodsBase($where=[],$field=[],$join=[]){
		$_where = array(
			'gb.status' => 0,
		);
		$where = array_merge($_where, $where);
		$_join = array(
		);
		if($field){
			$info = $this->alias('gb')
				->field($field)
				->join(array_merge($_join,$join))
				->where($where)
				->find();
		}else{
			$info = $this->alias('gb')
				->where($where)
				->join(array_merge($_join,$join))
				->find();
		}
		return $info;
	}
}