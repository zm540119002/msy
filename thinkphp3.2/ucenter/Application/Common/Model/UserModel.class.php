<?php
namespace Common\Model;

use Think\Model;
use Think\Model\RelationModel;

class UserModel extends Model {
    protected $tableName = 'user';
    protected $tablePrefix = '';
    protected $connection = 'DB_CONFIG1';

    protected $_validate = array(
        array('name','require','请填写用户名！'),
        array('name','','用户名已经存在！',0,'unique',self::MODEL_INSERT),
        array('name','accountIsExist','用户名不存在！',0,'function',self::MODEL_UPDATE),
        array('pass_word','require','请填写密码！'),
        array('pass_word','checkPwd','密码格式不正确',0,'function',self::MODEL_BOTH),
        array('re_password','pass_word','密码与确认密码不一致',0,'confirm'),
        array('mobile_phone','require','请填写手机号码！'),
        array('mobile_phone','isMobile','手机号格式不正确',0,'function',self::MODEL_BOTH),
        array('mobile_phone','','此手机号已被注册，请更换手机号码！',0,'unique',self::MODEL_INSERT),
//        array('mobile_phone','isReservedMobilePhone','不是预留手机号码！',0,'function',self::MODEL_UPDATE),//总出错
        array('captcha','require','请填写验证码！'),
    );

    //新增
    public function addUser(){
        if(!IS_POST){
            return errorMsg(C('NOT_POST'));
        }
        unset($_POST['id']);

        $res = $this->create($_POST,1);
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
    public function saveUser($where=array()){
        if(!IS_POST){
            return errorMsg(C('NOT_POST'));
        }

        $name = I('post.name','','string');
        if(!$name){
            return errorMsg('确少参数用户名');
        }

        $res = $this->create($_POST,2);
        if(!$res){
            return errorMsg($this->getError());
        }

        $_where = array(
            'name' => $name,
        );
        $_where = array_merge($_where,$where);
       
        $res = $this->where($_where)->save();
        
        if($res === false){
            return errorMsg($this->getError());
        }

        $returnArray = array(
            'id' => $res,
        );

        return successMsg('修改成功',$returnArray);
    }

    //标记删除
    public function delUser($where=array()){
        if(!IS_POST){
            return errorMsg(C('NOT_POST'));
        }
        unset($_POST['id']);

        $id = I('post.userId',0,'int');
        if(!$id){
            return errorMsg('确少参数userId');
        }

        $_where = array(
            'id' => $id,
        );
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
    public function selectUser($where=[],$field=[],$join=[]){
        $_where = array(
            'u.status' => 0,
        );
        $_field = array(
            'u.id','u.name','u.nickname','u.status','u.mobile','u.level','u.type','u.password','u.role_id',
            'u.avatar','u.sex','u.birthday','u.last_login_time','u.create_time','u.update_time',
        );
        $_join = array(
        );
        $list = $this
            ->alias('u')
            ->where(array_merge($_where,$where))
            ->field(array_merge($_field,$field))
            ->join(array_merge($_join,$join))
            ->select();
        return $list?:[];
    }
}