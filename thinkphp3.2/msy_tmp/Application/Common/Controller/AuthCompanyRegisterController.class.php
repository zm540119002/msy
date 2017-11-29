<?php
namespace Common\Controller;

use Common\Cache\CompanyCache;

/**机构登记的控制器基类
 */
class AuthCompanyRegisterController extends AuthUserController{
    protected $company = null;  //分公司
    protected $companyRegisterUrl = 'UserCenter/CompanyRegister/registerInfo';    //机构登记URL

    public function __construct(){
        parent::__construct();

        CompanyCache::remove($this->user['id']);
        $this->company = CompanyCache::get($this->user['id']);
        //机构登记
        if(!$this->company || $this->company['auth_status'] == 0){
            $this->error(C('ERROR_COMPANY_REGISTER_REMIND'),U($this->companyRegisterUrl));
        }
    }

    //上传机构形象照
    public function uploadCompanyFigure(){
        $img = isset($_POST['img'])? $_POST['img'] : '';
        // 获取图片
        list($type, $data) = explode(',', $img);
        // 判断类型
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

        // 生成的文件名
        $uploadPath = C('UPLOAD_PATH');
        $companyPath = C('COMPANY_FIGURE_PATH');

        $storePath = $uploadPath.$companyPath;
        if(!mk_dir($storePath)){
            $this->ajaxReturn(errorMsg('创建目录失败'));
        }
        $storeName = time().$ext;

        $photo = $storePath . $storeName;

        $model = M('company');
        $where = array(
            'id' => $this->company['id'],
        );
        $field = array(
            'logo',
            'figure_url_0','figure_url_1','figure_url_2','figure_url_3',
            'figure_url_4','figure_url_5','figure_url_6','figure_url_7',
        );
        $res = $model->where($where)->field($field)->find();
        if(!$res){
            $this->ajaxReturn(errorMsg($model->getError()));
        }
        $uploadType = isset($_POST['uploadType'])?$_POST['uploadType']:'';
        if($res[$uploadType]){
            if (!unlink($uploadPath.$res[$uploadType])) {
                $this->ajaxReturn(errorMsg('删除图片失败'));
            }
        }

        // 生成文件
        $returnData = file_put_contents($photo, base64_decode($data), true);
        if(false === $returnData){
            $this->ajaxReturn(errorMsg('保存文件失败'));
        }

        $image = new \Think\Image();
        $image->open($photo);
        $image->thumb(370, 660,\Think\Image::IMAGE_THUMB_SCALE)->save($photo);

        $data = array(
            $uploadType => $companyPath . $storeName,
        );
        $res = $model->where($where)->setField($data);
        if(false === $res){
            $this->ajaxReturn(errorMsg($model->getError()));
        }

        $this->ajaxReturn(successMsg($companyPath . $storeName));
    }
}


