<?php
/**type    0：所有|1：系统|2：普通
 * display    1：显示|0：隐藏
 */
return [
    'menu' => [
        'system'=>[
            'id'=>10,'name'=>'系统管理','type'=>1,
            'sub_menu' => [
                ['id'=>11,'name'=>'节点管理','display'=>0,'controller'=>'Node','action'=>'manage',],
                ['id'=>12,'name'=>'角色管理','display'=>1,'controller'=>'Role','action'=>'manage',],
                ['id'=>13,'name'=>'权限管理','display'=>0,'controller'=>'Role','action'=>'empower',],
                ['id'=>14,'name'=>'用户管理','display'=>1,'controller'=>'User','action'=>'manage',],
            ],
        ],
    ],
];