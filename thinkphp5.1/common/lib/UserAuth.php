<?php
namespace common\lib;

/**系统用户的登录/退出/是否登录判断
 */
class UserAuth{
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

    /**购物车-cookie-商品-入库
     */
    public static function saveCookieCartToMysql($userId,$moduleName){
        $modelCart = D($moduleName.'/Cart');
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


