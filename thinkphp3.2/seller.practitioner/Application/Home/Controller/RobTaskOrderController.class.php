<?php
namespace Home\Controller;

class RobTaskOrderController extends  BaseAuthSellerController {
    //抢单首页
    public function index(){
        if(IS_POST){
        }else{
            $this->display();
        }
    }

    //任务单详情
    public function taskOrderDetail(){
        if(IS_POST){
        }else{
            $taskOrderId = I('get.taskOrderId',0,'int');

            //应征状态
            $enlistStatus = $this->isEnlist($taskOrderId);
            $this->assign('enlistStatus',$enlistStatus);

            //任务单信息
            $taskOrder = $this->getTaskOrderInfoById($taskOrderId);
            $this->assign('taskOrder',$taskOrder);

            $this->display();
        }
    }

    //应征
    public function enlist(){
        if(!IS_POST){
            $this->ajaxReturn(errorMsg('请用POST方式访问！'));
        }
        $taskOrderId = I('post.taskOrderId',0,'int');
        if($this->isEnlist($taskOrderId)){
            $this->ajaxReturn(errorMsg('已应征过此任务单了！'));
        }
        $model = M('task_order_seller_enlist_signing');
        $data = array(
            'task_order_id' => $taskOrderId,
            'seller_id' => $this->seller['id'],
            'create_time' => time(),
        );
        $task_order_seller_enlist_signing_id = $model->add($data);
        if($task_order_seller_enlist_signing_id === false){
            $this->ajaxReturn(errorMsg($model->getError()));
        }
        $this->ajaxReturn(successMsg('应征成功'));
    }

    //任务单列表
    public function taskOrderList(){
        if(IS_POST){
        }else{
            $taskOrderType = I('get.taskOrderType',0,'int');
            $this->assign('taskOrderType',$taskOrderType);
            $this->display();
        }
    }

    //获取分页任务单数据
    public function getTaskOrderList(){
        if(!IS_GET){
            $this->ajaxReturn(errorMsg('请用GET方式访问！'));
        }
        $model = M('task_order t');
        $where = array(
            't.status' => 0,
            't.sign_status' => 0,
            't.online' => 1,
        );
        if(isset($_GET['taskOrderType']) && is_numeric($_GET['taskOrderType'])){
            $where['t.type'] = intval($_GET['taskOrderType']);
        }
        $keyword = I('get.keyword','','string');
        if($keyword){
            $where['_complex'] = array(
                't.name' => array('LIKE', '%' . $keyword . '%'),
                't.no' => array('LIKE', '%' . $keyword . '%'),
                'c.name' => array('LIKE', '%' . $keyword . '%'),
                '_logic' => 'OR'
            );
        }

        $field = array(
            't.id','t.name','t.no','t.create_time','t.type','t.sign_status','t.expect_date',
            'c.name company_name','c.registrant','c.registrant_mobile','c.address','c.province','c.city','c.area'
        );
        $order = '';
        $join = array(
            ' left join company c on c.id = t.company_id '
        );
        $group = "";
        $pageSize = ( isset($_GET['pageSize']) && $_GET['pageSize'] ) ? I('get.pageSize',0,'int') : C('DEFAULT_PAGE_SIZE');

        //资源限额-每天分配任务不超过10个（含搜索服务）
        $request_quota = C('REQUEST_QUOTA');
        $model_user_request_quota = M('user_request_quota');
        $where_user_request_quota = array(
            'user_id' => $this->user['id'],
        );
        $field_user_request_quota = ' id,quota ';
        $user_request_quota = $model_user_request_quota
            ->where($where_user_request_quota)
            ->field($field_user_request_quota)
            ->find();
        if($user_request_quota === false){//出错的情况
            $this->ajaxReturn(errorMsg($model_user_request_quota->getError()));
        }
        if($user_request_quota){//有记录的情况
            if($user_request_quota['quota'] >= $request_quota){//限额返回
                $this->ajaxReturn(errorMsg('你今天的任务单请求机会用完了，请改日再试。（每天限额10个）'));
            }
            if($request_quota - $user_request_quota['quota'] < $pageSize){
                $pageSize = $request_quota - $user_request_quota['quota'];
            }
        }

        if($keyword){
            $taskOrderList = $model->where($where)->field($field)->join($join)->select();
        }else{
            $taskOrderList = page_query($model,$where,$field,$order,$join,$group,$pageSize);
            $taskOrderList = $taskOrderList['data'];
        }

        $countList = count($taskOrderList);
        if(!$countList){
            $this->ajaxReturn(successMsg($taskOrderList));
        }

        //新增or更新-请求限额
        if(!$user_request_quota){//无记录的情况-新增
            $_data = array(
                'user_id' => $this->user['id'],
                'quota' => $countList,
            );
            $res = $model_user_request_quota->add($_data);
            if(!$res){
                $this->ajaxReturn(errorMsg('新增限额记录失败'));
            }
        }else{//有记录的情况-更新
            $where = array(
                'user_id' => $this->user['id'],
                'id' => $user_request_quota['id'],
            );
            $res = $model_user_request_quota->where($where)->setInc('quota',$countList);
            if($res===false){
                $this->ajaxReturn(errorMsg($model_user_request_quota->getError()));
            }
        }

        $this->assign('taskOrderList',$taskOrderList);
        $this->display('Public:taskOrderList');
    }
}