<?php
namespace Common\Cache;


class CompanyCache{
    private static $_cache_key = 'cache_company_';

    /**
     * 从缓存中获取信息
     * @param $store_id
     * @param $type = 0 为机构, = 1 为分公司
     * @return mixed
     */
    public static function get($user_id){
        $company = S(CompanyCache::$_cache_key.$user_id);
        if(!$company){
            $where = array(
                'user_id' => $user_id,
                'type' => 0,
                'status' => 0,
            );
            $field = array(
                'id','name','shorten_name','intro','logo','scale','type','status','auth_status',
                'father_id','user_id','province','city','area','address','telephone','level',
                'registrant','registrant_mobile','consignee','consignee_mobile',
                'license_url','id_url','id_reverse_url','unit_certificate_url','create_time',
                'figure_url_0','figure_url_1','figure_url_2','figure_url_3',
                'figure_url_4','figure_url_5','figure_url_6','figure_url_7',
            );
            $company = M('company')->where($where)->field($field)->find();
            S(CompanyCache::$_cache_key.$user_id, $company, array('type'=>'file', 'expire'=>C('DEFAULT_EXPIRE')));
        }
        return $company;
    }

    public static function remove($id){
        S(CompanyCache::$_cache_key.$id, null);
    }
}

