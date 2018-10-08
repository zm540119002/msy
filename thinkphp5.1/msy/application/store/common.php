<?php
//获取用户-工厂-角色-权限节点ID
function getUserFactoryRoleNode($userId,$factoryId){
    if(!intval($userId) || !intval($factoryId)){
        return errorMsg('参数错误');
    }
    $modelUserFactoryRole = new \app\store\model\UserFactoryRole();
    $roleList = $modelUserFactoryRole->getRole($userId,$factoryId);
    $modelRoleNode = new \app\store\model\RoleNode();
    $list = $modelRoleNode->getList(array_column($roleList,'id'));
    return $list;
}
//获取店铺类型
function getStoreType($num){
    return $num?config('custom.store_type')[$num]:'保留';
}
//获取店铺经营类型
function getRunType($num){
    return $num?config('custom.run_type')[$num]:'保留';
}