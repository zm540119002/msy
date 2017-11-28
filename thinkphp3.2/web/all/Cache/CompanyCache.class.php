<?php
namespace web\all\Cache;


class CompanyCache{
    private static $_cache_key = 'cache_company_';

    /**
     * 从缓存中获取信息
     * @param $type = 0 为机构, = 1 为分公司
     */
    public static function get($user_id){
        $company = S(CompanyCache::$_cache_key.$user_id);
        if(!$company){
            $where = array(
                'user_id' => $user_id,
                'type' => 0,
                'status' => 0,
            );
            $modelCompany = D('Company');
            $company = $modelCompany->selectCompany($where);
            $company = $company[0];
            S(CompanyCache::$_cache_key.$user_id, $company, array('type'=>'file', 'expire'=>C('DEFAULT_EXPIRE')));
        }
        return $company;
    }

    public static function remove($id){
        S(CompanyCache::$_cache_key.$id, null);
    }
}

