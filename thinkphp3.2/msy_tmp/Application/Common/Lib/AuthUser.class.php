<?php
namespace Common\Lib;

/**
 * 系统用户的登录/退出/是否登录判断
 * Class Authuser
 * @package Common\Lib
 */
class Authuser{
    /**
     * check login status
     * @return bool|mixed
     */
    public static function check(){
        $user = session('user');
        $user_sign = session('user_sign');
        if (!$user || !$user_sign) {
            return false;
        }
        if ($user_sign != data_auth_sign($user)) {
            return false;
        }
        return $user;
    }

    /**
     * login check
     * @param $data
     * @return bool
     */
    public static function getUser($data){
        $where = array(
            'mobile' => $data['mobile'],
            'status' => 0,
        );
        $field = ' id,name,nickname,passwd,salt,mobile,status,type,avatar,last_login_time ';
        $user = M('user')
            ->field($field)
            ->where($where)
            ->find();
        if(empty($user)) {
            return false;
        }
        if(!slow_equals( $user['passwd'], md5($user['salt'] . $data['passwd']) )) {
            return false;
        }

        return $user;
    }

    /**
     * 更新登录信息
     * @param $user
     */
    public static function updateLoginInfo($user_id){
        $where = array(
            'id' => $user_id,
        );
        M('user')->where($where)->setField('last_login_time', time());
    }

    /**
     * set login Session
     * @param $user
     */
    public static function setSession($user){
        $auth = array(
            'id' => $user['id'],
            'type' => $user['type'],
            'name' => $user['name'],
            'nickname' => $user['nickname'],
            'mobile' => $user['mobile'],
            'last_login_time' => $user['last_login_time'],
            'avatar' => $user['avatar'],
            'rand' => create_random_str(10, 0),
        );
        session('user', $auth);
        session('user_sign', data_auth_sign($auth));
    }

    /**获取登录信息
     * @return mixed
     */
    public static function getSession(){
        return session('user');
    }
    /**
     * remove login cookie
     */
    public static function removeLogin(){
        session('user', null);
        session('user_sign', null);
    }

    /*
     * 验证手机号是否存在
     * $mobile 手机号
     */

    public static function mobileIsExist($mobile){
        return M('user')->where(array('mobile'=>$mobile))->count();
    }
}


