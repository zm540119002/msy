<?php
namespace web\all\Cache;

class AgentCache{
    private static $_cache_key = 'cache_agent_';

    /**从缓存中获取信息
     */
    public static function get($user_id){
        $agent = S(self::$_cache_key.$user_id);
        if(!$agent){
            $where = array(
                'user_id' => $user_id,
                'status' => 0,
                'auth_status' => 1,
            );
            $modelAgent = D('Business/Agent');
            $agent = $modelAgent->selectAgent($where);
            $agent = $agent[0];
            S(self::$_cache_key.$user_id, $agent, array('type'=>'file', 'expire'=>C('DEFAULT_EXPIRE')));
        }
        return $agent;
    }

    /**从缓存中获取信息
     */
    public static function getByMobilePhone($mobilePhone){
        $agent = S(self::$_cache_key.$mobilePhone);
        if(!$agent){
            $where = array(
                'mobile_phone' => $mobilePhone,
                'status' => 0,
                'auth_status' => 1,
            );
            $modelAgent = D('Agent');
            $agent = $modelAgent->selectAgent($where);
            $agent = $agent[0];
            S(self::$_cache_key.$mobilePhone, $agent, array('type'=>'file', 'expire'=>C('DEFAULT_EXPIRE')));
        }
        return $agent;
    }

    /**删除缓存信息
     */
    public static function remove($id){
        S(self::$_cache_key.$id, null);
    }

    /**删除缓存信息
     */
    public static function removeByMobilePhone($mobilePhone){
        S(self::$_cache_key.$mobilePhone, null);
    }
}

