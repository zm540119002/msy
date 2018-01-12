<?php
namespace Common\Model;
use Think\Model;
use web\all\Component\WxpayAPI\Jssdk;

class WeiXinModel extends Model {
    protected $tableName = 'wx_user';
    protected $tablePrefix = '';
    protected $connection = 'DB_CONFIG1';

    protected $_validate = array(
    );

    //新增
    public function addWeiXinUser(){
        unset($_POST['id']);
        $res = $this->create();
        if(!$res){
            return errorMsg($this->getError());
        }
        $id = $this->add();
        if($id === false){
            return errorMsg($this->getError());
        }
        $returnArray = array(
            'id' => $id,
        );
        return successMsg('新增成功',$returnArray);
    }

    //修改
    public function saveWeiXinUser($where=array()){
        unset($_POST['id']);
        $res = $this->create();
        if(!$res){
            return errorMsg($this->getError());
        }
        $_where = array(
            'status' => 0,
        );
        if(isset($_POST['WeiXinUserId']) && intval($_POST['WeiXinUserId'])){
            $id = I('post.WeiXinUserId',0,'int');
        }
        if($id){
            $_where['id'] = $id;
        }
        $_where = array_merge($_where,$where);
        $res = $this->where($_where)->save();
        if($res === false){
            return errorMsg($this->getError());
        }
        $returnArray = array(
            'id' => $id,
        );
        return successMsg('修改成功',$returnArray);
    }

    //标记删除
    public function delWeiXinUser($where=array()){
        unset($_POST['id']);
        $_where = array(
            'status' => 0,
        );
        $id = I('post.WeiXinUserId',0,'int');
        if($id){
            $_where['id'] = $id;
        }
        $_where = array_merge($_where,$where);
        $res = $this->where($_where)->setField('status',2);
        if($res === false){
            return errorMsg($this->getError());
        }
        $returnArray = array(
            'id' => $id,
        );
        return successMsg('删除成功',$returnArray);
    }

    //查询
    public function selectWeiXinUser($where=[],$field=[],$join=[]){
        $_where = array(
            'wxu.status' => 0,
        );
        $_field = array(
            'wxu.id','wxu.openid','wxu.nickname','wxu.sex','wxu.country','wxu.province',
            'wxu.city','wxu.latitude','wxu.longitude','wxu.longitude','wxu.headimgurl','wxu.subscribe',
        );
        $_join = array(
        );
        $list = $this
            ->alias('wxu')
            ->where(array_merge($_where,$where))
            ->field(array_merge($_field,$field))
            ->join(array_merge($_join,$join))
            ->select();
        return $list?:[];
    }

    //微信登录
    public function wxLogin(){
        if(isWxBrowser()) {//判断是否为微信浏览器
            $wechat= new Jssdk(C('WX_CONFIG')['APPID'], C('WX_CONFIG')['APPSECRET']);
            $code = isset($_GET['code'])?$_GET['code']:'';
            if($code){
                $wxUser =$wechat ->getOauthUserInfo();
                $where = array(
                    'wxu.openid' => $wxUser['openid'],
                );
                $wxUserDatabase = $this -> selectWeiXinUser($where);
                if(!$wxUserDatabase){
                    $res = $this -> add($wxUser);
                    if(!$res){
                        return errorMsg($this->getError());
                    }
                }else{
                    return $wxUser;
                }
            }
        }

    }

}