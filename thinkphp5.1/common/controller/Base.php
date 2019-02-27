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
        $this->host = http_type() . (isset($_SERVER['HTTP_X_FORWARDED_HOST']) ? $_SERVER['HTTP_X_FORWARDED_HOST'] :
            (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : ''));
        session('backUrl',$_SERVER['REQUEST_URI'] ? $this->host . $_SERVER['REQUEST_URI'] : $this->host . $_SERVER['HTTP_REFERER']);
        //多步跳转后回原发起页
        session('returnUrl',input('get.returnUrl','')?:input('post.returnUrl',''));
                if(isWxBrowser() && !request()->isAjax()) {//判断是否为微信浏览器
            $weiXinUserInfo =  session('weiXinUserInfo');
            if(empty($weiXinUserInfo)){
                $mineTools = new \common\component\payment\weixin\Jssdk(config('wx_config.appid'), config('wx_config.appsecret'));
                $weiXinUserInfo = $mineTools->getOauthUserInfo();
                print_r($weiXinUserInfo);exit;
                session('weiXinUserInfo',$weiXinUserInfo);
            }
            $this -> assign('weiXinUserInfo',$weiXinUserInfo);
        }
    }
    //返回图片临时相对路径
    public function uploadFileToTemp(){
        $postData = $_POST;
        if(is_string($postData['fileBase64'])){
            if(strpos($postData['fileBase64'],'data:image') !==false || strpos($postData['fileBase64'],'data:video') !== false){
                $fileName =  $this ->_uploadSingleFileToTemp($postData['fileBase64']);
                if(isset($fileName['status'])&& $fileName['status'] == 0){
                    return $fileName;
                }
            }
            return successMsg($fileName);
        }
        if(is_array($postData['fileBase64'])){
            $filesNew = [];
            foreach ($postData['fileBase64'] as $k=>$file){
                //判断是否为base64编码图片
                if(strpos($file,'data:image') !==false || strpos($file,'data:video') !== false){
                    $fileName = $this ->_uploadSingleFileToTemp($file);
                    if(isset($fileName['status'])&& $fileName['status'] == 0){
                        return $fileName;
                    }
                    $filesNew[] = $fileName;
                }else{
                    $filesNew[] = $file;
                }
            }
            return successMsg($filesNew);
        }
    }
    //返回图片临时相对路,上传多张图片带描述
    public function uploadMultiFileToTempWithDes(){
        $files = $_POST['imgsWithDes'];
        $filesNew = [];
        foreach ($files as $k=>$file){
            //判断是否为base64编码图片
            if(strpos($file['fileSrc'],'data:image') !==false || strpos($file['fileSrc'],'data:video') !== false){
                $fileName =  $this ->_uploadSingleFileToTemp($file['fileSrc']);
                if(isset($fileName['status'])&& $fileName['status'] == 0){
                    return $fileName;
                }
                $filesNew[$k]['fileSrc'] = $fileName;
                $filesNew[$k]['fileText'] = $file['fileText'];
            }else{
                $filesNew[$k] = $file;
            }
        }
        return json_encode($filesNew);
    }

    //上传单个data64位文件
    private function _uploadSingleFileToTemp($fileBase64){
        // 获取图片
        list($type, $data) = explode(',', $fileBase64);
        // 判断文件类型
        list($fileType,$ext) = explode('/', $type);
        $array = [
            'data:image/jpg;base64',
            'data:image/gif;base64',
            'data:image/png;base64',
            'data:image/jpeg;base64',
            'data:video/mp4;base64',
            'data:video/rm;base64',
            'data:video/mtv;base64',
            'data:video/wmv;base64',
            'data:video/avi;base64',
            'data:video/3gp;base64',
            'data:video/flv;base64',
            'data:video/rmvb;base64',
        ];
        if(in_array($type,$array)){
            $ext = explode(';', $ext);
            $ext = '.'.$ext[0];
        }
        if(!$ext){
            return errorMsg('不支持此文件格式');
        }
        //文件大小 单位M
        $fileSize = strlen($data)/1024/1024;
        //图片限制大小
        if($fileType == 'data:image'){
            if($fileSize >3){//大于2M
                return errorMsg('请上传小于2M的图片');
            }
        }
        //视频限制大小
        if($fileType == 'data:video'){
            if($fileSize > 10){//大于10
                return errorMsg('请上传小于10M的视频');
            }
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
        $fileName = generateSN(5) . $ext;
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
        return $tempRelativePath . $fileName;
    }

  


}