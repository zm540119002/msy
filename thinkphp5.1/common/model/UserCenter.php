<?php
namespace common\model;

class UserCenter extends \think\Model {
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
		$data['mobile_phone'] = trim($data['mobile_phone']);
		$data['password'] = trim($data['password']);
		$validateUser = new \common\validate\User;
		if($data['mobile_phone'] && $data['password']){//账号密码登录
			if(!$validateUser->scene('login')->check($data)) {
				return errorMsg($validateUser->getError());
			}
			return $this->_login($data['mobile_phone'],$data['password']);
		}else{
			return errorMsg('登录信息不完善！');
		}
	}

	/**注册
	 */
	public function register(){
		$data = input('post.');
		$data['mobile_phone'] = trim($data['mobile_phone']);
		$data['password'] = trim($data['password']);
		$data['captcha'] = trim($data['captcha']);
		if(!$this->_checkCaptcha($data['mobile_phone'],$data['captcha'])){
			return errorMsg('验证码错误，请重新获取验证码！');
		}
		$validateUser = new \common\validate\User;
		if(!$validateUser->scene('register')->check($data)) {
			return errorMsg($validateUser->getError());
		}
		if(!$this->_register($data['mobile_phone'],$data['password'])){
			return errorMsg('注册失败');
		}
		return $this->_login($data['mobile_phone'],$data['password']);
	}

	/**重置密码
	 */
	public function resetPassword(){
		$postData = input('post.');
		$validateUser = new \common\validate\User;
		if(!$validateUser->scene('resetPassword')->check($postData)){
			return errorMsg($validateUser->getError());
		}
		if($postData['mobile_phone'] && $postData['captcha']){
			if(!$this->_checkCaptcha($postData['mobile_phone'],$postData['captcha'])){
				return errorMsg('验证码错误，请重新获取验证码！');
			}
			if(!$this->_checkAccountExist($postData['mobile_phone'])){
				return errorMsg('账号不存在！');
			}
			$saveData['salt'] = create_random_str(10,0);//盐值
			$saveData['password'] = md5($saveData['salt'] . $postData['pass_word']);//加密
			$where = array(
				'status' => 0,
				'mobile_phone' => $postData['mobile_phone'],
			);
			$response = $this->where($where)->update($saveData,$where);
			if(!$response){
				return errorMsg('重置失败！');
			}
			return successMsg('重置成功');
		}
		return errorMsg('资料缺失！');
	}

	/**登录
	 */
	private function _login($mobilePhone,$password){
		$user = $this->_get($mobilePhone,$password);
		if(!$user){
			return errorMsg('账号或密码错误,请重新输入！');
		}
		//更新最后登录时间
		$this->_setLastLoginTimeById($user['id']);
		return successMsg($this->_setSession($user));
	}

	/**注册
	 */
	private function _register($mobilePhone,$passWord){
		$data['mobile_phone'] = $mobilePhone;
		$salt = create_random_str(10,0);
		$data['salt'] = $salt;//盐值;
		$data['password'] = md5($salt . $passWord);//加密;
		$data['name'] = 'msy_' . create_random_str(9,3);
		$data['nickname'] = '游客';
		$data['create_time'] = time();
		$this->save($data);
		if(!$this->getAttr('id')){
			return false;
		}
		return true;
	}

	/**更新-最后登录时间
	 */
	private function _setLastLoginTimeById($userId){
		$where = array(
			'id' => $userId,
		);
		$this->where($where)->setField('last_login_time', time());
	}

	/**获取登录信息
	 */
	private function _get($mobilePhone,$password){
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
			->find()->toArray();
		if(!is_array($user) || empty($user)) {
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
		//返回发起页或平台首页
		$backUrl = session('backUrl');
		$pattern  =  '/index.php\/([A-Z][a-z]*)\//' ;
		preg_match ($pattern,$backUrl,$matches);
		return $backUrl?(is_ssl()?'https://':'http://').$backUrl:url('login');
	}
	
	/**检查验证码
	 */
	private function _checkCaptcha($mobilePhone,$captcha){
		return true;//上线后再验证
		return session('captcha_' . $mobilePhone) == $captcha ;
	}

	/**检查账号是否存在
	 */
	private function _checkAccountExist($mobilePhone){
		return count($this->where('mobile_phone','=',$mobilePhone)->select())?true:false;
	}
}