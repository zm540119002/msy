<?php
namespace Common\Controller;

use Think\Controller;

/**公共基础控制器
 */
class BaseController extends Controller{
    public function __construct(){
        parent::__construct();

        //登录验证后跳转回原验证发起页
//        session('backUrl',$_SERVER['HTTP_REFERER'] ? $_SERVER['HTTP_REFERER'] : $_SERVER['REQUEST_URI']);
        session('backUrl',$_SERVER['REQUEST_URI'] ? $_SERVER['REQUEST_URI'] : $_SERVER['HTTP_REFERER']);
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
            $this->ajaxReturn(errorMsg('只支持:jpeg,jpg,gif,png格式的图片'));
        }
        //上传公共路径
        $uploadPath = realpath(C('UPLOAD_PATH')) . '/' ;
        if(!is_dir($uploadPath)){
            if(!mk_dir($uploadPath)){
                $this->ajaxReturn(errorMsg('创建Uploads目录失败'));
            }
        }

        //临时相对路径
        $tempRelativePath = C('UPLOAD_IMG_TEMP');
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
            $image = new \Think\Image();
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
        $tempRelativePath = C('UPLOAD_IMG_TEMP');

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
        $newFile = $newPath .$filename;
        //重命名文件
        if(file_exists($tempFile)){//临时文件存在则移动
            if(!rename($tempFile,$newFile)){
                $this->ajaxReturn(errorMsg('重命名文件失败'));
            }
        }

        return $newRelativePath . $filename;
    }

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

    //KindEditor上传图片
    public function uploads(){
        //文件保存目录路径
        $save_path = 'Uploads/';
        //文件保存目录URL
        $save_url = 'Uploads/';
        //定义允许上传的文件扩展名
        $ext_arr = array(
            'image' => array('gif', 'jpg', 'jpeg', 'png', 'bmp'),
            'flash' => array('swf', 'flv'),
            'media' => array('swf', 'flv', 'mp3', 'wav', 'wma', 'wmv', 'mid', 'avi', 'mpg', 'asf', 'rm', 'rmvb'),
            'file' => array('doc', 'docx', 'xls', 'xlsx', 'ppt', 'htm', 'html', 'txt', 'zip', 'rar', 'gz', 'bz2'),
        );
        //最大文件大小
        $max_size = 1000000;
        $save_path = realpath($save_path) . '/';

        //PHP上传失败
        if (!empty($_FILES['imgFile']['error'])) {
            switch($_FILES['imgFile']['error']){
                case '1':
                    $error = '超过php.ini允许的大小。';
                    break;
                case '2':
                    $error = '超过表单允许的大小。';
                    break;
                case '3':
                    $error = '图片只有部分被上传。';
                    break;
                case '4':
                    $error = '请选择图片。';
                    break;
                case '6':
                    $error = '找不到临时目录。';
                    break;
                case '7':
                    $error = '写文件到硬盘出错。';
                    break;
                case '8':
                    $error = 'File upload stopped by extension。';
                    break;
                case '999':
                default:
                    $error = '未知错误。';
            }
            echo($error);
        }

        //有上传文件时
        if (empty($_FILES) === false) {
            //原文件名
            $file_name = $_FILES['imgFile']['name'];
            //服务器上临时文件名
            $tmp_name = $_FILES['imgFile']['tmp_name'];
            //文件大小
            $file_size = $_FILES['imgFile']['size'];
            //检查文件名
            if (!$file_name) {
                echo("请选择文件。");
            }
            //检查目录
            if (@is_dir($save_path) === false) {
                echo("上传目录不存在。");
            }
            //检查目录写权限
            if (@is_writable($save_path) === false) {
                echo("上传目录没有写权限。");
            }
            //检查是否已上传
            if (@is_uploaded_file($tmp_name) === false) {
                echo("上传失败。");
            }
            //检查文件大小
            if ($file_size > $max_size) {
                echo("上传文件大小超过限制。");
            }
            //检查目录名
            $dir_name = empty($_GET['dir']) ? 'image' : 'temp';
            if (empty($ext_arr[$dir_name])) {
                echo("目录名不正确。");
            }
            //获得文件扩展名
            $temp_arr = explode(".", $file_name);
            $file_ext = array_pop($temp_arr);
            $file_ext = trim($file_ext);
            $file_ext = strtolower($file_ext);
            //检查扩展名
            if (in_array($file_ext, $ext_arr[$dir_name]) === false) {
                echo("上传文件扩展名是不允许的扩展名。\n只允许" . implode(",", $ext_arr[$dir_name]) . "格式。");
            }
            //创建文件夹
            if ($dir_name !== '') {
                $save_path .= $dir_name . "/";
                $save_url .= $dir_name . "/";
                if (!file_exists($save_path)) {
                    mkdir($save_path);
                }
            }

            //新文件名
            $new_file_name = date("YmdHis") . '_' . rand(10000, 99999) . '.' . $file_ext;
            //移动文件
            $file_path = $save_path . $new_file_name;
            if (move_uploaded_file($tmp_name, $file_path) === false) {
                echo("上传文件失败。");
            }
            @chmod($file_path, 0644);
            $file_url ='/'. $save_url . $new_file_name;

            header('Content-type: text/html; charset=UTF-8');
            $this->ajaxReturn(array('error' => 0, 'url' => $file_url));
        }
    }
}


