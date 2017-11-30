<?php
namespace Home\Controller;

use Vendor\Qrcode\Qrcode;

//店家档案
class MallPartnerController extends BaseAuthCompanyController {
    //管理合伙人
    public function managePartner(){
        if(IS_POST){
        }else{
            $this->display();
        }
    }

    //管理项目
    public function manageProject(){
        if(IS_POST){
        }else{
            $this->display();
        }
    }

    //获取合伙人
    public function getPartner(){
        if(!IS_GET){
            $this->ajaxReturn(errorMsg('请用GET方式访问！'));
        }
        $model = M('partner p');
        $field = ' p.name,p.mobile_phone,p.level,d.c_name ';
        $where = array(
            'p.user_id' => $this->user['id'],
        );
        $order = '';
        $join = array(
            'left join dictionary d on p.level = d.id ',
        );
        $group = "";
        $pageSize = ( isset($_GET['pageSize']) && $_GET['pageSize'] ) ? I('get.pageSize',0,'int') : C('DEFAULT_PAGE_SIZE');
        $data = page_query($model,$where,$field,$order,$join,$group,$pageSize);
        $this->ajaxReturn(successMsg($data));
    }

    //搜索合伙人
    public function searchPartner(){
        if(!IS_GET){
            $this->ajaxReturn(errorMsg('请用GET方式访问！'));
        }
        $model = M('partner p');
        $field = ' p.name,p.mobile_phone,p.level,d.c_name ';
        $where = array(
            'p.user_id' => $this->user['id'],
        );
        $join = array(
            'left join dictionary d on p.level = d.id ',
        );
        $keyword = I('get.keyword','','string');
        if($keyword){
            $where['_complex'] = array(
                'p.name' => array('LIKE', '%' . $keyword . '%'),
                'p.mobile_phone' => array('LIKE', '%' . $keyword . '%'),
                '_logic' => 'OR'
            );
        }

        $data = $model->join($join)->field($field)->where($where)->select();
        $this->ajaxReturn(successMsg($data));
    }

    public function testQRcode(){
        $url = 'http://192.168.1.102/o2o/index.php/Cloudstore/Company/registerInfo';
        $url = 'http://www.baidu.com';
//        $url = 'http://119.75.217.109';
//        $url = 'http://192.168.1.102/o2o';

        //生成二维码图片
        $object = new Qrcode();
        $qrcodePath = WEB_URL.'Public/images/qrcode/';//保存文件路径
        $fileName = time().'.png';//保存文件名

        $outFile = $qrcodePath.$fileName;
        $level = 'L'; //容错级别
        $size = 6; //生成图片大小
        $frameSize = 2; //边框像素
        $saveAndPrint = true;
        $object->png($url, $outFile, $level, $size, $frameSize,$saveAndPrint);

        exit;
        $data = base64_encode(file_get_contents($outFile));
        echo '<img src="data:image/png;base64,'.$data.'" />';
        exit;
    }
}