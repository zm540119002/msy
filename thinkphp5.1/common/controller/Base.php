<?php
namespace common\controller;
use \common\component\image\Image;
/**基于公共基础控制器
 */
class Base extends \think\Controller{
    protected $host;
    public function __construct(){
        parent::__construct();
        //登录验证后跳转回原验证发起页
        $this->host = isset($_SERVER['HTTP_X_FORWARDED_HOST']) ? $_SERVER['HTTP_X_FORWARDED_HOST'] :
            (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '');
        session('backUrl',$_SERVER['REQUEST_URI'] ? $this->host . $_SERVER['REQUEST_URI'] : $this->host . $_SERVER['HTTP_REFERER']);
        //多步跳转后回原发起页
        session('returnUrl',input('get.returnUrl','')?:input('post.returnUrl',''));
    }
    //返回图片临时相对路径
    public function uploadImgToTemp(){
        $img = isset($_POST['img'])? $_POST['img'] : '';
        // 获取图片
        list($type, $data) = explode(',', $img);
        // 判断文件类型
        $ext = '';

        if(strstr($type,'image/jpeg')!=''){
            $ext = '.jpeg';
        }elseif(strstr($type,'image/jpeg')!=''){
            $ext = '.jpg';
        }elseif(strstr($type,'image/gif')!=''){
            $ext = '.gif';
        }elseif(strstr($type,'image/png')!=''){
            $ext = '.png';
        }elseif(strstr($type,'video/mp4')!=''){
            $ext = '.mp4';
        }
        if(!$ext){
            return errorMsg('只支持:jpeg,jpg,gif,png格式的图片');
        }
        //上传公共路径
        $uploadPath = config('upload_dir.upload_path');
        if(!is_dir($uploadPath)){
            if(!mk_dir($uploadPath)){
                return  errorMsg('创建Uploads目录失败');
            }
        }
        $uploadPath = realpath($uploadPath);
        if($uploadPath === false){
            return  errorMsg('获取Uploads实际路径失败');
        }
        $uploadPath = $uploadPath . '/' ;
        //临时相对路径
        $tempRelativePath = config('upload_dir.temp_path');
        //存储路径
        $storePath = $uploadPath . $tempRelativePath;
        if(!mk_dir($storePath)){
            return errorMsg('创建临时目录失败');
        }
        //文件名
        $fileName = time() . $ext;
        //带存储路径的文件名
        $photo = $storePath . $fileName;
        // 生成文件
        $returnData = file_put_contents($photo, base64_decode($data), true);
        if(false === $returnData){
            return errorMsg('保存文件失败');
        }
//        //压缩文件
        if( isset($_POST['imgWidth']) || isset($_POST['imgHeight']) ){
            $imgWidth = isset($_POST['imgWidth']) ? intval($_POST['imgWidth']) : 150;
            $imgHeight = isset($_POST['imgHeight']) ? intval($_POST['imgHeight']) : 150;
            $image = Image::open($photo);
            $image->thumb($imgWidth, $imgHeight,Image::THUMB_SCALING)->save($photo);
        }
        return successMsg($tempRelativePath . $fileName);
    }
    //返回图片临时相对路,上传多张图片
    public function uploadMultiImgToTemp(){
        $imgs = $_POST['imgs'];
        $imgsNew = [];
        foreach ($imgs as $k=>$img){
            //判断是否为base64编码图片
            if(strpos($img,'data:image') !==false || strpos($img,'data:video') !== false){
                // 获取图片
                list($type, $data) = explode(',', $img);
                // 判断文件类型
                $ext = '';
                if(strstr($type,'image/jpeg')!=''){
                    $ext = '.jpeg';
                }elseif(strstr($type,'image/jpg')!=''){
                    $ext = '.jpg';
                }elseif(strstr($type,'image/gif')!=''){
                    $ext = '.gif';
                }elseif(strstr($type,'image/png')!=''){
                    $ext = '.png';
                }elseif(strstr($type,'video/mp4')!=''){
                    $ext = '.mp4';
                }
                if(!$ext){
                    return errorMsg('只支持:jpeg,jpg,gif,png格式的图片');
                }
                //上传公共路径
                $uploadPath = config('upload_dir.upload_path');
                if(!is_dir($uploadPath)){
                    if(!mk_dir($uploadPath)){
                        return  errorMsg('创建Uploads目录失败');
                    }
                }
                $uploadPath = realpath($uploadPath);
                if($uploadPath === false){
                    return  errorMsg('获取Uploads实际路径失败');
                }
                $uploadPath = $uploadPath . '/' ;

                //临时相对路径
                $tempRelativePath = config('upload_dir.temp_path');
                //存储路径
                $storePath = $uploadPath . $tempRelativePath;
                if(!mk_dir($storePath)){
                    return errorMsg('创建临时目录失败');
                }
                //文件名
                $fileName = time().$k . $ext;
                //带存储路径的文件名
                $photo = $storePath . $fileName;

                // 生成文件
                $returnData = file_put_contents($photo, base64_decode($data), true);
                if(false === $returnData){
                    return errorMsg('保存文件失败');
                }
                //压缩文件
                if( isset($_POST['imgWidth']) || isset($_POST['imgHeight']) ){
                    $imgWidth = isset($_POST['imgWidth']) ? intval($_POST['imgWidth']) : 150;
                    $imgHeight = isset($_POST['imgHeight']) ? intval($_POST['imgHeight']) : 150;
                    $image = Image::open($photo);
                    $image->thumb($imgWidth, $imgHeight,Image::THUMB_SCALING)->save($photo);
                }
                $imgsNew[] = $tempRelativePath . $fileName;
            }else{
                $imgsNew[] = $img;
            }
        }
       return successMsg($imgsNew);
    }

    //返回图片临时相对路,上传多张图片带描述
    public function uploadMultiImgToTempWithDes(){
        //$img = isset($_POST['imgs'])? $_POST['imgs'] : '';
        $imgs = $_POST['imgsWithDes'];
        $imgsNew = [];
        foreach ($imgs as $k=>$img){
            //判断是否为base64编码图片
            if(strpos($img['imgSrc'],'data:image') !==false || strpos($img['imgSrc'],'data:video') !== false){
                // 获取图片
                list($type, $data) = explode(',', $img['imgSrc']);
                // 判断文件类型
                $ext = '';
                if(strstr($type,'image/jpeg')!=''){
                    $ext = '.jpeg';
                }elseif(strstr($type,'image/jpg')!=''){
                    $ext = '.jpg';
                }elseif(strstr($type,'image/gif')!=''){
                    $ext = '.gif';
                }elseif(strstr($type,'image/png')!=''){
                    $ext = '.png';
                }elseif(strstr($type,'video/mp4')!=''){
                    $ext = '.mp4';
                }
                if(!$ext){
                    return errorMsg('只支持:jpeg,jpg,gif,png格式的图片');
                }
                //上传公共路径
                $uploadPath = config('upload_dir.upload_path');
                if(!is_dir($uploadPath)){
                    if(!mk_dir($uploadPath)){
                        return  errorMsg('创建Uploads目录失败');
                    }
                }
                $uploadPath = realpath($uploadPath);
                if($uploadPath === false){
                    return  errorMsg('获取Uploads实际路径失败');
                }
                $uploadPath = $uploadPath . '/' ;

                //临时相对路径
                $tempRelativePath = config('upload_dir.temp_path');
                //存储路径
                $storePath = $uploadPath . $tempRelativePath;
                if(!mk_dir($storePath)){
                    return errorMsg('创建临时目录失败');
                }
                //文件名
                $fileName = time().$k . $ext;
                //带存储路径的文件名
                $photo = $storePath . $fileName;

                // 生成文件
                $returnData = file_put_contents($photo, base64_decode($data), true);
                if(false === $returnData){
                    return errorMsg('保存文件失败');
                }
                //压缩文件
                //压缩文件
                if( isset($_POST['imgWidth']) || isset($_POST['imgHeight']) ){
                    $imgWidth = isset($_POST['imgWidth']) ? intval($_POST['imgWidth']) : 150;
                    $imgHeight = isset($_POST['imgHeight']) ? intval($_POST['imgHeight']) : 150;
                    $image = Image::open($photo);
                    $image->thumb($imgWidth, $imgHeight,Image::THUMB_SCALING)->save($photo);
                }
                $imgsNew[$k]['imgSrc'] = $tempRelativePath . $fileName;
                $imgsNew[$k]['imgText'] = $img['imgText'];
            }else{
                $imgsNew[$k] = $img;
            }
        }
        return json_encode($imgsNew);
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
            'filename'=>'goods',   //保存目录  ./uploads/compose/goods....
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
        $dir = './uploads/compose/'.$init['filename'].'/'.date('Y').'/'.date('m');
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