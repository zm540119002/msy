<?php
namespace Mall\Controller;
use web\all\Controller\AuthUserController;
class CommentController extends AuthUserController {
    /**
     *用户评论
     */
    public function CommentEdit(){
        $model = D('Comment');
        if(IS_POST){
            if(isset($_POST['commentId']) && intval($_POST['commentId'])){//修改
                $res = $model->saveComment();
            }else{//新增
                if(empty($_POST['title'])){
                    $_POST['title'] = '此用户没有填写标题';
                }
                if(empty($_POST['score'])){
                    $this->ajaxReturn($this->error('请评分'));
                }
                if(empty($_POST['content'])){
                    $this->ajaxReturn($this->error('请填写评价内容'));
                }
                $_POST['create_time'] = time();
                $_POST['update_time'] = time();
                $_POST['user_id'] = $this->user['id'];
                $_POST['user_name'] = $this->user['name'];
                $model->startTrans();
                $res = $model->addComment();//添加评论表
                if (!$res['id']) {
                    $model->rollback();
                }
                $_POST['logistics_status']=4;
                $_POST['update_time']=time();
                $where['id'] = $_POST['order_id'];
                $res = D('Order')->where($where)->saveOrder();//添加评论表
                if ($res['status']!=1) {
                    $model->rollback();
                }
            }
            $model->commit();//提交事务
            $this->ajaxReturn($res);
        }else{
            $this->orderId = $_GET['orderId'];
            $this->aveScore = round($model->avg('score'),1);//平均分数
            $this->userCommentNum =$model->count();//多少用户评价
            $scoreInfo = $model->field('score,count(*) as num')->group('score')->select();//按评分分组
            foreach ($scoreInfo as $key =>$value){
                $scoreInfo[$key]['percent'] = intval(($value['num']/$this->userCommentNum)*100);
            }
            $scoreBase = C('COMMENT_ARRAY');
            foreach ($scoreBase as $k =>$vo){
                foreach ($scoreInfo as $kk =>$vv){
                   if($vo['score'] == $vv['score']){
                       $scoreBase[$k]['num']=$vv['num'];
                       $scoreBase[$k]['percent'] =$vv['percent'];
                   }
                }
            }
            $this->scoreBase = $scoreBase;
            $this->display();
        }
    }

    //商品列表
    public function commentList(){
        if(!IS_GET){
            $this->ajaxReturn(errorMsg(C('NOT_GET')));
        }
        $model = D('Comment');
        $where = array(
            'c.status' => 0,
        );
        $field = array(
            'c.id','c.user_id','c.score','c.order_id','c.title','c.content',
            'c.create_time','c.update_time','c.user_name'
        );
        $join = array(
        );
        $group = "";
        $order = 'c.id';
        $pageSize = (isset($_GET['pageSize']) && intval($_GET['pageSize'])) ? I('get.pageSize',0,'int') : C('DEFAULT_PAGE_SIZE');
        $this->currentPage = (isset($_GET['p']) && intval($_GET['p'])) ? I('get.p',0,'int') : 1;
        $commentList = page_query($model,$where,$field,$order,$join,$group,$pageSize,$alias='c');
        $this->commentList = $commentList['data'];
        $templateType = I('get.templateType','','string');
        if($templateType=='list'){
            $this ->display('commentListTpl');
        }
    }
}