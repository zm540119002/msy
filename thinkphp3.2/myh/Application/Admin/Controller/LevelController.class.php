<?php
namespace Admin\Controller;

use web\all\Controller\BaseController;

class LevelController extends BaseController {
    /**级别分类-管理
     */
    public function levelManage(){
        $model = D('Level');
        if(IS_POST){
            $this->levelList = $model->selectLevel();
            $this->display('levelList');
        }else{
            $this->levelList = $model->selectLevel();
            $this->display();
        }
    }

    /**级别分类-编辑
     */
    public function levelEdit(){
        $modelLevel = D('Level');
        if(IS_POST){
            if(isset($_POST['levelId']) && intval($_POST['levelId'])){
                //修改的情况，先判断数据库
                $levelInfo = array();
                if( isset($_POST['img']) && $_POST['img'] ){
                    $_POST['img'] = $this->moveImgFromTemp(C('PURCHASER_LEVEL_IMG'),basename($_POST['img']));//更换图片
                    $this->delImgFromPaths($levelInfo['img'],$_POST['img']);
                }
                if( isset($_POST['detail_img']) && $_POST['detail_img'] ){//更换图片
                    $_POST['detail_img'] = $this->moveImgFromTemp(C('PURCHASER_LEVEL_IMG'),basename($_POST['detail_img']));
                    $this->delImgFromPaths($levelInfo['detail_img'],$_POST['detail_img']);
                }
                if( isset($_POST['star_img']) && $_POST['star_img'] ){//更换图片
                    $_POST['star_img'] = $this->moveImgFromTemp(C('PURCHASER_LEVEL_IMG'),basename($_POST['star_img']));
                    $this->delImgFromPaths($levelInfo['star_img'],$_POST['star_img']);
                }
                $res = $modelLevel->saveLevel();
            }else{
                if( isset($_POST['img']) && $_POST['img'] ){//新增图片
                    $_POST['img'] = $this->moveImgFromTemp(C('PURCHASER_LEVEL_IMG'),basename($_POST['img']));
                }
                if( isset($_POST['detail_img']) && $_POST['detail_img'] ){//新增图片
                    $_POST['detail_img'] = $this->moveImgFromTemp(C('PURCHASER_LEVEL_IMG'),basename($_POST['detail_img']));
                }
                if( isset($_POST['star_img']) && $_POST['star_img'] ){//新增图片
                    $_POST['star_img'] = $this->moveImgFromTemp(C('PURCHASER_LEVEL_IMG'),basename($_POST['star_img']));
                }
                $res = $modelLevel->addLevel();
            }
            $this->ajaxReturn($res);
        }else{
            if (isset($_GET['levelId']) && intval($_GET['levelId'])){
                $levelId = I('get.levelId', 0, 'int');
                $where = array(
                    'l.id' => $levelId,
                );
                $levelInfo = $modelLevel->selectLevel($where);
                $this->levelInfo = $levelInfo[0];
            }
            $this->levelStarList = C('LEVEL_STAR');
            $this->display();
        }
    }

    //级别列表
    public function levelList(){
        if(!IS_GET){
            $this->ajaxReturn(errorMsg(C('NOT_GET')));
        }

        $modelLevel = D('Level');
        $where = array(
            'l.status' => 0,
        );
        $keyword = I('get.keyword','','string');
        if($keyword){
            $where['_complex'] = array(
                'l.name' => array('like', '%' . trim($keyword) . '%'),
            );
        }
        $field = array(
        );
        $join = array(
        );
        $order = 'l.id';
        $group = "";
        $pageSize = (isset($_GET['pageSize']) && intval($_GET['pageSize'])) ? I('get.pageSize',0,'int') : C('DEFAULT_PAGE_SIZE');

        $levelList = page_query($modelLevel,$where,$field,$order,$join,$group,$pageSize,$alias='l');
        $this->levelList = $levelList['data'];
        $this->pageList = $levelList['pageList'];
        $this->display();
    }

    /**级别分类-删除
     */
    public function delLevel(){
        if(!IS_POST){
            $this->ajaxReturn(errorMsg(C('NOT_POST')));
        }
        $modelLevel = D('Level');
        $res = $modelLevel->delLevel();
        $this->ajaxReturn($res);
    }
}