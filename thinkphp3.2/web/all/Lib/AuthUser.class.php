<?php
namespace web\all\Lib;

/**系统用户的登录/退出/是否登录判断
 */
class Authuser{
    public static function aa(){
        echo 'aa';
    }
    /**检查是否登录
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

    /**获取用户信息
     */
    public static function getUser($data){
        $where = array(
            'status' => 0,
        );
        if(isset($data['name']) && $data['name']){
            $where['name'] = $data['name'];
        }elseif(isset($data['mobile_phone']) && $data['mobile_phone']){
            $where['mobile_phone'] = $data['mobile_phone'];
        }else{
            return false;
        }
        $field = ' id,name,nickname,mobile_phone,status,type,password,avatar,sex,salt,birthday,last_login_time ';
        $model = D('User');
        $user = $model
            ->field($field)
            ->where($where)
            ->find();
        if(empty($user)) {
            return false;
        }
        if(!slow_equals( $user['password'], md5($user['salt'] . $data['password']))) {
            return false;
        }
        return $user;
    }

    /**更新最后登录时间
     */
    public static function saveLastLoginTimeById($user_id){
        $where = array(
            'id' => $user_id,
        );
        D('User')->where($where)->setField('last_login_time', time());
    }

    /**设置登录session
     */
    public static function setSession($user){
        $auth = array(
            'id' => $user['id'],
            'name' => $user['name'],
            'nickname' => $user['nickname'],
            'mobile_phone' => $user['mobile_phone'],
            'status' => $user['status'],
            'level' => $user['level'],
            'type' => $user['type'],
            'avatar' => $user['avatar'],
            'sex' => $user['sex'],
            'birthday' => $user['birthday'],
            'last_login_time' => $user['last_login_time'],
            'rand' => create_random_str(10, 0),
        );
        session('user', $auth);
        session('user_sign', data_auth_sign($auth));
    }

    /**删除登录session
     */
    public static function removeLogin(){
        session('user', null);
        session('user_sign', null);
    }

    /**购物车-cookie-商品-入库
     */
    public static function saveCookieCartToMysql($userId){
        $modelCart = D('Cart');
        $cookieCarts = unserialize(cookie('cart'));
        $time = time();
        $ret = true;
        $modelCart->startTrans();
        if(!empty($cookieCarts)){
            $where = array(
                'ct.user_id' => $userId,
            );
            $mysqlCarts = $modelCart->selectCart($where);
            foreach ($cookieCarts as $cookieCart){
                $find = false;//假定没找到
                if(!empty($mysqlCarts)){
                    foreach ($mysqlCarts as $mysqlCart){
                        if($cookieCart['foreign_id'] == $mysqlCart['foreign_id']){//数据库存在，则修改
                            $find = true;//找到了
                            $where = array(
                                'user_id' => $userId,
                                'id' => $mysqlCart['id'],
                                'foreign_id' => $mysqlCart['foreign_id'],
                            );
                            $_POST['num'] = $cookieCart['num'] + $mysqlCart['num'];
                            $res = $modelCart->saveCart($where);
                            if($res['status']== 0){
                                $modelCart->rollback();
                                $ret = false;
                                break 2;
                            }
                        }
                    }
                }
                if(!$find){//如果没找到，则新增
                    $_POST = [];
                    $_POST['user_id'] = $userId;
                    $_POST['foreign_id'] = $cookieCart['foreign_id'];
                    $_POST['num'] = $cookieCart['num'];
                    $_POST['create_time'] = $time;
                    $res = $modelCart->addCart();
                    if($res['status']== 0){
                        $modelCart->rollback();
                        $ret = false;
                        break;
                    }
                }
            }
        }
        //入库成功清楚购物车cookie
        $ret && cookie('cart',null);
        $modelCart->commit();
        return $ret;
    }
}


