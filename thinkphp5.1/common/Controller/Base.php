<?php
namespace common\controller;

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

        //压缩文件
        if( isset($_POST['imgWidth']) || isset($_POST['imgHeight']) ){
            $imgWidth = isset($_POST['imgWidth']) ? intval($_POST['imgWidth']) : 150;
            $imgHeight = isset($_POST['imgHeight']) ? intval($_POST['imgHeight']) : 150;
            $image = new \think\Image();
            $image->open($photo);
            $image->thumb($imgWidth, $imgHeight,\Think\Image::IMAGE_THUMB_SCALE)->save($photo);
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
            $image = new \think\Image();
            $image->open($photo);
            $image->thumb($imgWidth, $imgHeight,\Think\Image::IMAGE_THUMB_SCALE)->save($photo);
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
                if( isset($_POST['imgWidth']) || isset($_POST['imgHeight']) ){
                    $imgWidth = isset($_POST['imgWidth']) ? intval($_POST['imgWidth']) : 150;
                    $imgHeight = isset($_POST['imgHeight']) ? intval($_POST['imgHeight']) : 150;
                    $image = new \think\Image();
                    $image->open($photo);
                    $image->thumb($imgWidth, $imgHeight,\Think\Image::IMAGE_THUMB_SCALE)->save($photo);
                }
                $imgsNew[$k]['imgSrc'] = $tempRelativePath . $fileName;
                $imgsNew[$k]['imgText'] = $img['imgText'];
            }else{
                $imgsNew[$k] = $img;
            }
        }
        return json_encode($imgsNew);
    }
}