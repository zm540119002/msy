<?php
namespace Mall\Controller;
use web\all\Controller\AuthUserController;
class CommentController extends AuthUserController {
    /**
     *
     */
    public function EditComment(){
        $model = D('Comment');
        if(IS_POST){
            if(isset($_POST['commentId']) && intval($_POST['commentId'])){//修改
                $res = $model->saveComment();
            }else{//新增
                $_POST['create_time'] = time();
                $_POST['update_time'] = time();
                $_POST['user_id'] = $this->user['id'];
                $_POST['user_name'] = $this->user['name'];
                $res = $model->addComment();
            }
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
}