<?php
namespace web\all\Cache;


class PartnerCache{
    private static $_cache_key = 'cache_partner_';
    
    /**从缓存中获取信息
     */
    public static function get($user_id){
        $partner = S(PartnerCache::$_cache_key.$user_id);
        if(!$partner){
            $where = array(
                'user_id' => $user_id,
                'type' => 0,
                'status' => 0,
            );
            $modelPartner = D('Partner');
            $partner = $modelPartner->selectPartner($where);
            $partner = $partner[0];
            S(PartnerCache::$_cache_key.$user_id, $partner, array('type'=>'file', 'expire'=>C('DEFAULT_EXPIRE')));
        }
        return $partner;
    }

    /**删除缓存信息
     */
    public static function remove($id){
        S(PartnerCache::$_cache_key.$id, null);
    }
}

