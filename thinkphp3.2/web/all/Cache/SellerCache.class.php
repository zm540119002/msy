<?php
namespace web\all\Cache;

class SellerCache{
    private static $_cache_key = 'cache_seller_';

    /**
     * 从缓存中获取信息
     * @return mixed
     */
    public static function get($user_id){
        $seller = S(SellerCache::$_cache_key.$user_id);
        if(!$seller){
            $model = M('seller s');
            $where = array(
                'user_id' => $user_id,
                'status' => 0,
            );
            $field = array(
                's.id','s.name','s.nickname','s.mobile_phone','s.type','s.status','s.auth_status','s.user_id',
                's.working_years','s.sex','s.province','s.city','s.intro','s.goods_skills','s.avatar','s.birthday',
                's.id_front_img','s.id_reverse_img','s.face_front_img','s.figure_url_0','s.figure_url_1',
                's.figure_url_2','s.figure_url_3','s.figure_url_4','s.figure_url_6','s.figure_url_7','s.create_time',
            );
            $seller = $model->where($where)->field($field)->find();
            $config = array(
                'type'=>'file',
                'expire'=> C('DUFAULT_EXPIRE'),
            );
            S(SellerCache::$_cache_key.$user_id, $seller, $config);
        }
        return $seller;
    }

    public static function remove($user_id){
        S(SellerCache::$_cache_key.$user_id, null);
    }
}

