<?php
namespace app\index\controller;

class TwoDimensionalCode extends \common\controller\UserBase {

    /**
     * 获取二维码
     * @return array
     * @throws \think\exception\PDOException
     */
    public function getUserQRcode()
    {
        if(!request()->isAjax()){
            return errorMsg('请求方式错误');
        }
        $ids = input('post.ids/a');
        $config = [
            'where'=>[
                ['id','in',$ids],
                ['status','=',0]
            ],'field'=>[
                'id','headline','specification','thumb_img','bulk_price','rq_code_url'
            ],
        ];
        $model = new \app\index_admin\model\Goods();
        $list = $model -> getList($config);
        return $this->generateQRcode($list);

        //$this->successMsg('成功',['url'=>config('custom.pay_gateway').$sn,'id'=>$id]);
    }

    public function generateQRcode(){
        $uploadPath = realpath( config('upload_dir.upload_path')) . '/';
        $url = request()->domain().'/uid/'.$this->user['id'];
        $newRelativePath = config('upload_dir.hss_user_QRCode');
        $shareQRCode = createLogoQRcode($url,$newRelativePath);
        $init = [
            'save_path'=>$newRelativePath,   //保存目录  ./uploads/compose/goods....
            'name'=> $this->user['name'], //用户名
            'avatar'=> $uploadPath.$this->user['avatar'],//用户头像
            'base_map'=> request()->domain().'/static/index/img/base_map.png', // 460*534  分享底图
            'qrcode'=>$uploadPath.$shareQRCode, // 120*120
            'font'=>'./static/font/simhei.ttf',   //字体
        ];
        $res =  $this->compose($init);
        return $res;
//        if($res['status'] == 1){
//            $newQRCodes = $res['info'];
//            $model = new \app\index_admin\model\Goods();
//            $res= $model->where(['id'=>$info['id']])->setField(['rq_code_url'=>$newQRCodes]);
//            if(false === $res){
//                return $this->errorMsg('失败');
//            }
//            unlink($uploadPath.$shareQRCodes);
//            if(!empty($oldQRCodes)){
//                unlink($uploadPath.$oldQRCodes);
//            }
//        }

    }

    /**合成商品图片
     *
     * @param array $config 合成图片参数
     * @return $img->path 合成图片的路径
     *
     */
    public function compose(array $config=[])
    {
        $init = $config;
        $avatar = $this->imgInfo($init['avatar']);
        $baseMap = $this->imgInfo($init['base_map']);
        $qrcode = $this->imgInfo($init['qrcode']);
        if( !$avatar || !$baseMap || !$qrcode){
            return $this->errorMsg('提供的图片问题');
        }
        $im = imagecreatetruecolor(480, 780);  //图片大小
        $color = imagecolorallocate($im, 0xFF,0xFF,0xFF);
        $text_color = imagecolorallocate($im, 87, 87, 87);
        $text_color1 = imagecolorallocate($im, 137, 137, 137);
        $red_color = imagecolorallocate($im, 230, 0, 18);
        imagefill($im, 0, 0, $color);
        imagettftext($im, 20, 0, 100, 35, $text_color, $init['font'], $init['name']); //名字
        imagecopyresized($im, $baseMap['obj'], 10, 10, 0, 0, 90, 60, $baseMap['width'], $logoImg['height'] );  //平台logo
        imagecopyresized($im, $avatar['obj'], 20, 710, 0, 0, 20, 20, $avatar['width'], $RMB_logo['height'] );  //
        imagecopyresized($im, $qrcode['obj'], 330, 630, 0, 0, 140, 140, $qrcode['width'], $qrcode['height'] );  //二维

        $dir = config('upload_dir.upload_path').'/'.$init['save_path'].'compose/';
        if(!is_dir($dir)){
            mkdir($dir, 0777, true);
        }
        $filename = generateSN(5).'.jpg';
        $file = $dir.$filename;
        if( !imagejpeg($im, $file, 90) ){
            return $this->errorMsg('合成图片失败');
        }
        imagedestroy($im);
        return  successMsg($init['save_path'].'compose/'.$filename);
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