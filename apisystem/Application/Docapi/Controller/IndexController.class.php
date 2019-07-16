<?php
// +----------------------------------------------------------------------
// | ApiSystem接口文档管理系统 让沟通更简单
// | Copyright (c) 2015 http://www.apisystem.cn
// | Author: Texren  QQ: 174463651
// |         Smith77 QQ: 3246932472
// | 交流QQ群 577693968 交流QQ群2 460098419
// +----------------------------------------------------------------------

namespace Docapi\Controller;
use Think\Controller;
class IndexController extends ApisystemController {

    public function index(){
        //redirect(U('/Docapi/opapi/index'));


        $cat_list=S('show_catlist');
        if(!$cat_list){
            $cat_list= json_encode(getChildCat());
            S('show_catlist',$cat_list,300);
        }
        $cat_list=json_decode($cat_list,true);
        $this->assign('title','api接口文档管理系统');
        $this->assign('show_catlist', $cat_list);
        $this->display();
    }

    private function catList($cid=0)
    {
        $cid = intval($cid);
        $name = 'categoryapi';
        $map=array('status'=>1);
        $catlist = M($name)
            /* 查询指定字段，不指定则查询所有字段 */
            ->field(' * ')
            // 查询条件
            ->where($map)
            /* 执行查询 */
            ->select();
        Vendor('CatTree');
        $TreeView =new \CatTree();
        $TreeView->setArr($catlist);
        $trees = $TreeView->deep = 4;
        $trees = $TreeView->getTree(0,'');
        return $trees;



    }



    public function cache_catlist($flag=0){
        $cat_list= json_encode(getChildCat());
        S('show_catlist',$cat_list,300);
        if($flag==0){
            echo 'Refresh cache success';
        }else{
            return true;
        }

    }


}