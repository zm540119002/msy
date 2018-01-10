<?php
namespace web\all\Cache;

class AgentCache{
    private static $_cache_key = 'cache_agent_';

    /**从缓存中获取信息
     */
    public static function get($user_id){
        $agent = S(AgentCache::$_cache_key.$user_id);
        if(!$agent){
            $where = array(
                'user_id' => $user_id,
                'status' => 0,
            );
            $modelAgent = D('Agent');
//            $agent = $modelAgent->selectAgent($where);
//            $agent = $agent[0];
            S(AgentCache::$_cache_key.$user_id, $agent, array('type'=>'file', 'expire'=>C('DEFAULT_EXPIRE')));
        }
        return $agent;
    }

    /**删除缓存信息
     */
    public static function remove($id){
        S(AgentCache::$_cache_key.$id, null);
    }
}

