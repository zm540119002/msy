<?php
namespace app\common\validate;

use think\Validate;

class Goods extends Validate
{

    /**
     * @var array
     * CREATE TABLE `goods` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
    `no` varchar(18) NOT NULL DEFAULT '' COMMENT '商品编号',
    `name` varchar(60) NOT NULL DEFAULT '' COMMENT '商品名称',
    `category_id_1` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '一级分类id',
    `category_id_2` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '二级分类id',
    `category_id_3` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '三级分类id',
    `notices` varchar(2500) NOT NULL COMMENT '注意事项',
    `status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '状态：0 ：启用 1：违规（禁售） 2：删除',
    `on_off_line` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '上下架：0：保留 1：上架 2：下架',
    `sort` tinyint(5) NOT NULL DEFAULT '0' COMMENT '排序',
    `price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '商品价格',
    `discount_price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '优惠价',
    `special_price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '工作室特惠价',
    `tag` varchar(50) NOT NULL DEFAULT '' COMMENT '商品标签',
    `prewarning_value` tinyint(3) unsigned NOT NULL DEFAULT '100' COMMENT '库存预警值',
    `inventory` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '商品库存',
    `click_amount` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '历史点击数量',
    `sales_amount` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '历史销售数量',
    `announcements` varchar(2500) NOT NULL COMMENT '注意事项',
    `collect_amount` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '历史收藏数量',
    `specification` varchar(100) NOT NULL DEFAULT '' COMMENT '商品规格',
    `param` text NOT NULL COMMENT '商品参数',
    `usage` varchar(2000) NOT NULL COMMENT '使用方法',
    `intro` text NOT NULL COMMENT '商品简介',
    `main_img` varchar(100) NOT NULL DEFAULT '' COMMENT '商品主图',
    `detail_img` varchar(2000) NOT NULL DEFAULT '' COMMENT '详情图片',
    `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '添加时间',
    `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '编辑时间',
    `praise_level` tinyint(3) unsigned NOT NULL DEFAULT '5' COMMENT '好评星级',
    `praise_amount` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '评价数',
    `audit` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '商品审核：0 审核中 1：通过 2：未通过',
    `buy_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 普通订单,2 促销优惠, 3 团购 , ',
    `referrer_commission` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '推客提成',
    `agency_commission` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '代理商提成',
    `partner_commission` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '合伙人提成',
    `cash_back` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '团购返现',
    PRIMARY KEY (`id`)
    ) ENGINE=InnoDB AUTO_INCREMENT=62 DEFAULT CHARSET=utf8 COMMENT='商品表';
     */
    protected $rule = [
        'name'  =>  'require|max:100',
        'agent' =>  'require|max:50',
        'business_license' =>  'require|max:50',
        'auth_letter' =>  'require|max:150',
    ];
    protected $message  =   [
        'name.require' => '产商全称必须填写',
        'name.max'     => '产商全称最多不能超过25个字符',
        'category_id_1.require'   => '请选择所属分类',
        'business_license.require'   => '营业执照必须上传',
        'business_license.max'   => '营业执照存在地址过长',
        'auth_letter.require'   => '授权信必须上传',
        'auth_letter.max'   => '授权信存在地址最多不能超过25个字符',
    ];
    
    protected $scene = [
        'add'   =>  ['name','agent','business_license','auth_letter'],
        'edit'  =>  ['name','agent','business_license','auth_letter'],
    ];


}