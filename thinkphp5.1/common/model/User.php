<?php
namespace common\model;

use think\Model;
use think\Db;

class User extends Model {
	// 设置当前模型对应的完整数据表名称
	protected $table = 'user';
	// 设置主键
	protected $pk = 'id';
	// 设置当前模型的数据库连接
	protected $connection = 'db_config_common';

	/**登录
	 */
	public function login(){
		$data = input('post.');
		$validateUser = new \common\validate\User;
		if($data['mobile_phone'] && $data['captcha']){//验证码登录
			if($this->_checkRegister($data['mobile_phone'])){//已注册，则登录
				if(!$validateUser->scene('sceneLoginCaptcha')->check($data)) {
					return errorMsg($validateUser->getError());
				}
//				if(!$this->_checkCaptcha($data['mobile_phone'],$data['captcha'],'login')){
//					return errorMsg('验证码错误，请重新获取验证码！');
//				}

			}else{//未注册，则注册
				if(!$validateUser->scene('register')->check($data)) {
					return errorMsg($validateUser->getError());
				}
			}
		}elseif($data['mobile_phone'] && $data['password']){//密码登录
			if(!$validateUser->scene('sceneLoginPassword')->check($data)) {
				return errorMsg($validateUser->getError());
			}
		}else{
			return errorMsg('登录信息不完善！');
		}
	}

	private function _getInfo($mobilePhone,$password=''){
		$where = array(
			'status' => 0,
			'mobile_phone' => $mobilePhone,
		);
		$field = array(
			'id','name','nickname','mobile_phone','status','type','password','avatar',
			'sex','salt','birthday','last_login_time',
		);
		$user = $this
			->field($field)
			->where($where)
			->find();
		if(empty($user)) {
			return false;
		}
		if($password && !slow_equals($user['password'],md5($user['salt'].$password))){
			return false;
		}
		return $user;
	}
	
	/**检查验证码
	 * @param $mobilePhone
	 * @param $captcha
	 * @param string $captcha_type
	 * @return bool
	 */
	private function _checkCaptcha($mobilePhone,$captcha,$captcha_type='login'){
		return session('captcha_' . $captcha_type . '_' . $mobilePhone) == $captcha ;
	}

	/**检查是否注册
	 * @param $mobilePhone
	 * @return bool
	 */
	private function _checkRegister($mobilePhone){
		return count($this->where('mobile_phone','=',$mobilePhone)->select())?true:false;
	}
}