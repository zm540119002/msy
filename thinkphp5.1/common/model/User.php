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
				return $this->_login($data['mobile_phone']);
			}else{//未注册，则注册
				if(!$validateUser->scene('register')->check($data)) {
					return errorMsg($validateUser->getError());
				}
//				if(!$this->_checkCaptcha($data['mobile_phone'],$data['captcha'],'register')){
//					return errorMsg('验证码错误，请重新获取验证码！');
//				}
				if(!$this->_register($data['mobile_phone'])){
					return errorMsg($this->getLastSql());
				}
				return $this->_login($data['mobile_phone']);
			}
		}elseif($data['mobile_phone'] && $data['password']){//密码登录
			if(!$validateUser->scene('sceneLoginPassword')->check($data)) {
				return errorMsg($validateUser->getError());
			}
			return $this->_login($data['mobile_phone'],$data['password']);
		}else{
			return errorMsg('登录信息不完善！');
		}
	}

	public function setPassword($mobilePhone,$captcha){
		$data = input('post.');
		$validateUser = new \common\validate\User;
		if($data['mobile_phone'] && $data['captcha']){
//			if(!$this->_checkCaptcha($data['mobile_phone'],$data['captcha'],'login')){
//				return errorMsg('验证码错误，请重新获取验证码！');
//			}
			$data['salt'] = create_random_str(10,0);//盐值
			$data['password'] = md5($_POST['salt'] . $_POST['pass_word']);//加密
		}
	}

	/**登录
	 * @param $mobilePhone
	 * @param string $password
	 * @return array
	 */
	private function _login($mobilePhone,$password=''){
		$user = $this->_get($mobilePhone,$password);
		if(!$user){
			return errorMsg('获取用户信息失败！');
		}
		//设置登录session
		$this->_setSession($user);
		//更新最后登录时间
		$this->_setLastLoginTimeById($user['id']);
		//返回发起页或平台首页
		$backUrl = session('backUrl');
		$pattern  =  '/index.php\/([A-Z][a-z]*)\//' ;
		preg_match ($pattern,$backUrl,$matches);
		return successMsg($backUrl?(is_ssl()?'https://':'http://').$backUrl:url('login'));
	}

	/**注册
	 * @param $mobilePhone
	 * @return array
	 */
	private function _register($mobilePhone){
		$data['mobile_phone'] = $mobilePhone;
		$data['create_time'] = time();
		$this->save($data);
		return $this->id;
	}

	/**更新最后登录时间
	 */
	private function _setLastLoginTimeById($userId){
		$where = array(
			'id' => $userId,
		);
		$this->where($where)->setField('last_login_time', time());
	}
	/**获取登录信息
	 * @param string $mobilePhone 手机号码
	 * @param string $password	密码
	 * @return array|bool|null|\PDOStatement|string|Model
	 */
	private function _get($mobilePhone,$password=''){
		if(!$mobilePhone) {
			return false;
		}
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

	/**设置登录session
	 */
	private function _setSession($user){
		$user = array_merge($user,array('rand' => create_random_str(10, 0),));
		session('user', $user);
		session('user_sign', data_auth_sign($user));
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