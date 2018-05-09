<?php
namespace app\factory\controller;

class Goods extends FactoryBase
{
    /**
     * @return array|mixed
     *商品编辑
     */
    public function edit()
    {
        $goodsBaseModel = new \app\factory\model\GoodsBase;//商品基础表
        $goodsModel = new \app\factory\model\Goods;//商品扩展模型
        if(request()->isPost()){
            //编辑商品基础表
            $goodsBaseModel -> startTrans();
            $result = $goodsBaseModel -> edit($this->factory['factory_id']);
            if(!$result['status']){
                $goodsBaseModel ->rollback();
                return errorMsg('失败');
            }
            //编辑商品表
            $goodsBaseId =  $result['id'];
            $result = $goodsModel -> edit($goodsBaseId,$this->factory['factory_id']);
            if(!$result['status']){
                $goodsModel ->rollback();
                return errorMsg('失败');
            }
            $goodsBaseModel ->commit();
            return successMsg('成功');

        }
        $categoryModel = new \app\index_admin\model\Category;
        $platformCategory = $categoryModel->selectFirstCategory();
        $this -> assign('platformCategory',$platformCategory);
        $seriesModel = new \app\factory\model\Series;
        $where = [
            ['factory_id','=',$this->factory['factory_id']],
        ];
        $seriesList = $seriesModel -> selectSeries($where,[],['sort'=>'desc','id'=>'desc',]);
        $this -> assign('seriesList',$seriesList);
        if(input('?goods_base_id')){
            $goodsBaseId = (int)input('goods_base_id');
            $where = [
               ['gb.factory_id','=',$this->factory['factory_id']],
               ['g.goods_base_id','=',$goodsBaseId],
            ];
            $file = [
                'g.goods_base_id,g.id,g.sale_price,g.sale_type,g.shelf_status,g.create_time,g.update_time,g.store_type,
                gb.name,gb.retail_price,gb.trait,gb.cat_id_1,gb.cat_id_2,gb.cat_id_3,
                gb.thumb_img,gb.goods_video,gb.main_img,gb.details_img,gb.tag,gb.create_time,gb.update_time,
                gb.parameters'
            ];
            $join =[
                ['goods_base gb','gb.id = g.goods_base_id'],
            ];
            $goodsInfo =  $goodsModel -> selectGoods($where,$file,$join);
            if(empty($goodsInfo)){
                $this->error('此产品已下架');
            }
            $catArray= $goodsInfo[0]['cat_id_1'].','.$goodsInfo[0]['cat_id_2'];
            $goodsInfo[0]['catArray'] = $catArray;
            $this -> assign('goodsInfo',$goodsInfo);
        }
        return $this->fetch();
    }


    /**
     * @return array|mixed
     *商品预览
     */
    public function preview()
    {
        $model = new \app\factory\model\Goods;
        if(input('?goods_id')){
            $goodsId = (int)input('goods_id');
            $where = [
                ['id','=',$goodsId],
            ];
            $goodsInfo =  $model -> getGoods($where);
            $this -> assign('goodsInfo',$goodsInfo);
        }
        return $this->fetch();
    }


    /**
     * 查出产商相关产品 分页查询
     */
    public function getList(){
        $model = new\app\factory\model\Goods;
        $where = [
            ['gb.factory_id','=',$this->factory['factory_id']],
        ];
        $list = $model -> pageQuery($where);
        $this->assign('list',$list);
        if(isset($_GET['pageType'])){
            if($_GET['pageType'] == 'promotion' ){//促销
                return $this->fetch('list_promotion');
            }
            if($_GET['pageType'] == 'shelf' ){//上架管理
                return $this->fetch('list_shelf');
            }
            if($_GET['pageType'] == 'manage' ){//管理
                return $this->fetch('list_manage');
            }
        }
    }
    //商品管理展示页
    public function manage(){
        $this->assign('factory',$this->factory);
        return $this->fetch();
    }
    //上下架设置
    public function setShelf(){
        if(request()->isPost()){
            $data = input();
            $model = new \app\factory\model\Goods;
            $result = $model->allowField(true)
                ->save($data, ['id' => $data['goodsId'],'store_type'=>$data['storeType']]);
           if(false !== $result){
               return successMsg('成功');
           }
        }
    }

    /**
     * 合成商品图片
     *
     * @param array $config 合成图片参数
     * @return $img->path 合成图片的路径
     *
     */
    public function compose(array $config=[])
    {
        $init = [
            'title'=>'美尚官方旗舰店',
            'type'=>'供应商自营',
            'slogan'=>"采购平台·省了即赚到！",
            'name'=>'产品名称即“品牌名称（brand name）”。',
            'introduce'=>'产品标识所用文字应当为规范中文。',
            'money'=>'￥ 68.56 元',
            'logo'=>'./static/common/img/ucenter_logo.png', // 60*55px
            'brand'=>'./static/common/img/compose/brand.png', // 160*55
            'goods'=>'./static/common/img/compose/goods.png', // 460*534
            'qrcode'=>'./static/common/img/compose/qrcode.png', // 120*120
            'font'=>'./static/font/simhei.ttf',   //字体
        ];
        $init = array_merge($init, $config);
        $logo = $this->imgInfo($init['logo']);
        $brand = $this->imgInfo($init['brand']);
        $goods = $this->imgInfo($init['goods']);
        $qrcode = $this->imgInfo($init['qrcode']);
        if(!$logo ||!$brand || !$goods || !$qrcode){
            return '提供的图片问题';
        }
        $im = imagecreatetruecolor(480, 780);  //图片大小
        $color = imagecolorallocate($im, 240, 255, 255);
        $text_color = imagecolorallocate($im, 0, 0, 0);
        imagefill($im, 0, 0, $color);
        imagettftext($im, 14, 0, 265, 35, $text_color, $init['font'], $init['title']); //XX官方旗舰店
        imagettftext($im, 12, 0, 265, 55, $text_color, $init['font'], $init['type']); //供应商自营
        imagettftext($im, 16, 0, 10,  96, $text_color, $init['font'], $init['slogan']);   //标语
        imagettftext($im, 14, 0, 10, 670, $text_color, $init['font'], $init['name']); //说明
        imagettftext($im, 12, 0, 10, 700, $text_color, $init['font'], $init['introduce']); //规格
        imagettftext($im, 12, 0, 10, 730, $text_color, $init['font'], $init['money']); //金额
        imagecopyresized($im, $logo['obj'], 10, 10, 0, 0, 60, 55, $logo['width'], $logo['height'] );  //平台logo
        imageline($im, 80, 10, 80, 65, $text_color); //划一条实线
        imagecopyresized($im, $brand['obj'], 95, 10, 0, 0, 160, 55, $brand['width'], $brand['height'] );  //店铺logo
        imagecopyresized($im, $goods['obj'], 10, 106, 0, 0, 460, 534, $goods['width'], $goods['height']);  //商品
        imagecopyresized($im, $qrcode['obj'], 350, 650, 0, 0, 120, 120, $qrcode['width'], $qrcode['height'] );  //二维
        $dir = './uploads/compose/'.date('Y').'/'.date('m');
        if(!is_dir($dir)){
            mkdir($dir, 0777, true);
        }
        $filename = $dir.'/'.time().mt_rand(1000, 9999).'.jpg';
        if( !imagejpeg($im, $filename, 90) ){
            return '合成图片失败';
        }
        imagedestroy($im);
        return  substr($filename, 1);
    }

    private function imgInfo($path)
    {
        $info = getimagesize($path);
        //检测图像合法性
        if (false === $info) {
            return false; //图片不合法
        }
        if($info[2]>3){
            return false; //不支持此图片类型
        }
        $type = image_type_to_extension($info[2], false);
        $fun = "imagecreatefrom{$type}";
        //返回图像信息
        if(!$fun) return false;
        return [
            'width'  => $info[0],
            'height' => $info[1],
            'type'   => $type,
            'mime'   => $info['mime'],
            'obj'    => $fun($path),
        ];
    }

}