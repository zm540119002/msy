<?php
namespace common\controller;

use think\controller;
use think\Image;

/**公共基础控制器
 */
class BaseController extends Controller{
    public function __construct(){
        parent::__construct();
        //登录验证后跳转回原验证发起页
        session('backUrl',$_SERVER['REQUEST_URI'] ? $this->host . $_SERVER['REQUEST_URI'] : $this->host . $_SERVER['HTTP_REFERER']);
        //多步跳转后回原发起页
        session('returnUrl',I('get.returnUrl','','string')?:I('post.returnUrl','','string'));
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
            $this->error('只支持:jpeg,jpg,gif,png格式的图片');
//            $this->ajaxReturn(errorMsg('只支持:jpeg,jpg,gif,png格式的图片'));
        }
        //上传公共路径
        $uploadPath = C('UPLOAD_PATH');
        if(!is_dir($uploadPath)){
            if(!mk_dir($uploadPath)){
                $this -> error('创建Uploads目录失败');
//                $this->ajaxReturn(errorMsg('创建Uploads目录失败'));
            }
        }
        $uploadPath = realpath($uploadPath);
        if($uploadPath === false){
            $this->ajaxReturn(errorMsg('获取Uploads实际路径失败'));
        }
        $uploadPath = $uploadPath . '/' ;

        //临时相对路径
        $tempRelativePath = C('TEMP_PATH');
        //存储路径
        $storePath = $uploadPath . $tempRelativePath;
        if(!mk_dir($storePath)){
            $this->ajaxReturn(errorMsg('创建临时目录失败'));
        }
        //文件名
        $fileName = time() . $ext;
        //带存储路径的文件名
        $photo = $storePath . $fileName;

        // 生成文件
        $returnData = file_put_contents($photo, base64_decode($data), true);
        if(false === $returnData){
            $this->ajaxReturn(errorMsg('保存文件失败'));
        }

        //压缩文件
        if( isset($_POST['imgWidth']) || isset($_POST['imgHeight']) ){
            $imgWidth = isset($_POST['imgWidth']) ? intval($_POST['imgWidth']) : 150;
            $imgHeight = isset($_POST['imgHeight']) ? intval($_POST['imgHeight']) : 150;
            $image = new Image();
            $image->open($photo);
            $image->thumb($imgWidth, $imgHeight,\Think\Image::IMAGE_THUMB_SCALE)->save($photo);
        }
        $this->ajaxReturn(successMsg($tempRelativePath . $fileName));
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

    //新增图片对比数据库，删除不同的图片
    public function delImgFromPaths($oldImgPaths,$newImgPaths){
        //上传文件公共路径
        $uploadPath = realpath(C('UPLOAD_PATH')) . '/';
        if(!is_dir($uploadPath)){
            $this->ajaxReturn(errorMsg('目录：'.$uploadPath.'不存在！'));
        }

        if(is_string($oldImgPaths) && is_string($newImgPaths)){
            if($oldImgPaths !== $newImgPaths){
                if(!file_exists($uploadPath . $oldImgPaths)){
                    $this->ajaxReturn(errorMsg('旧文件不存在！'));
                }
                if(!unlink($uploadPath . $oldImgPaths)){
                    $this->ajaxReturn(errorMsg('删除旧文件失败！'));
                }
            }
        }elseif(is_array($oldImgPaths) && is_array($newImgPaths)){
            $delImgPaths = array_diff($oldImgPaths,$newImgPaths);
            foreach ($delImgPaths as $delImgPath) {
                if(!file_exists($uploadPath . $delImgPath)){
                    $this->ajaxReturn(errorMsg('旧文件不存在！'));
                }
                if(!unlink($uploadPath . $delImgPath)){
                    $this->ajaxReturn(errorMsg('删除旧文件失败！'));
                }
            }
        }
    }

    //删除图片
    public function delImg($imgPaths){
        //上传文件公共路径
        $uploadPath = realpath(C('UPLOAD_PATH')) . '/';
        if(!is_dir($uploadPath)){
            $this->ajaxReturn(errorMsg('目录：'.$uploadPath.'不存在！'));
        }
        if(is_string($imgPaths)){
            if(!file_exists($uploadPath . $imgPaths)){
                $this->ajaxReturn(errorMsg('旧文件不存在！'));
            }
            if(!unlink($uploadPath . $imgPaths)){
                $this->ajaxReturn(errorMsg('删除旧文件失败！'));
            }
        }elseif(is_array($imgPaths) ){
            foreach ($imgPaths as $delImgPath) {
                if(!file_exists($uploadPath . $delImgPath)){
                    $this->ajaxReturn(errorMsg('文件不存在！'));
                }
                if(!unlink($uploadPath . $delImgPath)){
                    $this->ajaxReturn(errorMsg('删除文件失败！'));
                }
            }
        }
    }
}




