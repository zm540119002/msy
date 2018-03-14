<?php
namespace common\controller;

use think\Controller;
use think\Image;

/**公共基础控制器
 */
class Base extends Controller{
    protected $host;
    public function __construct(){
        parent::__construct();
        //登录验证后跳转回原验证发起页
        $this->host = isset($_SERVER['HTTP_X_FORWARDED_HOST']) ? $_SERVER['HTTP_X_FORWARDED_HOST'] :
            (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '');
        session('backUrl',$_SERVER['REQUEST_URI'] ? $this->host . $_SERVER['REQUEST_URI'] : $this->host . $_SERVER['HTTP_REFERER']);
        //多步跳转后回原发起页
//        session('returnUrl',I('get.returnUrl','','string')?:I('post.returnUrl','','string'));
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
        $uploadPath = config('uploadDir.upload_path');
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
        $tempRelativePath = config('uploadDir.temp_path');
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
//        if( isset($_POST['imgWidth']) || isset($_POST['imgHeight']) ){
//            $imgWidth = isset($_POST['imgWidth']) ? intval($_POST['imgWidth']) : 150;
//            $imgHeight = isset($_POST['imgHeight']) ? intval($_POST['imgHeight']) : 150;
//            $image = new Image();
//            $image->open($photo);
//            $image->thumb($imgWidth, $imgHeight,\Think\Image::IMAGE_THUMB_SCALE)->save($photo);
//        }
        return successMsg($tempRelativePath . $fileName);
    }

    /**从临时目录里移动文件到新的目录
     * @param $newRelativePath 新相对路径
     * @param $filename 文件名
     * @return string 返回相对文件路径
     */
    public function moveImgFromTemp($newRelativePath,$filename){
        //上传文件公共路径
        $uploadPath = realpath(C('UPLOAD_PATH')) . '/';
        if(!is_dir($uploadPath)){
            if(!mk_dir($uploadPath)){
                $this->ajaxReturn(errorMsg('创建Uploads目录失败'));
            }
        }

        //临时相对路径
        $tempRelativePath = C('TEMP_PATH');

        //旧路径
        $tempPath = $uploadPath . $tempRelativePath;
        if(!is_dir($tempPath)){
            $this->ajaxReturn(errorMsg('临时目录不存在！'));
        }
        //旧文件
        $tempFile = $tempPath . $filename;

        //新路径
        $newPath = $uploadPath . $newRelativePath;
        if(!mk_dir($newPath)){
            $this->ajaxReturn(errorMsg('创建新目录失败'));
        }
        //新文件
        $newFile = $newPath . $filename;
        //重命名文件
        if(file_exists($tempFile)){//临时文件存在则移动
            if(!rename($tempFile,$newFile)){
                $this->ajaxReturn(errorMsg('重命名文件失败'));
            }
        }
        return $newRelativePath . $filename;
    }

   
}




