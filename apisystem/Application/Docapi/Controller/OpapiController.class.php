<?php
// +----------------------------------------------------------------------
// | ApiSystem接口文档管理系统 让沟通更简单
// | Copyright (c) 2015 http://www.apisystem.cn
// | Author: Texren  QQ: 174463651
// |         Smith77 QQ: 3246932472
// | 交流QQ群 577693968 交流QQ群2 460098419
// +----------------------------------------------------------------------

namespace Docapi\Controller;
use Common\Model\TreeViewModel;
use Think\Controller;
class OpapiController extends ApisystemController {

    /**
     * 初始化方法
     */
    protected function _initialize(){
        parent::_initialize();

        $show_catlist=S('show_catlist');
        if(!$show_catlist){
            $show_catlist= json_encode(getChildCat());
            S('show_catlist',$show_catlist,300);
        }
        $show_catlist=json_decode($show_catlist,true);
        $this->assign('show_catlist', $show_catlist);
    }

    public function index($p = 0){
        $page = intval($p);
        $page = $page ? $page : 1; //默认显示第一页数据

        $name = 'docapi';
        $row    =  100 ;
        $map=array('status'=>'1');
        $data = M($name)
            /* 查询指定字段，不指定则查询所有字段 */
            ->field(' * ')
            // 查询条件
            ->where($map)
            /* 默认通过id逆序排列 */
            ->order(' project_id ASC,id ASC')
            /* 数据分页 */
            ->page($page, $row)
            /* 执行查询 */
            ->select();
        foreach($data as $k=>$v){
            $catUrl=  $this->catUrl($v['project_id'],1);
            $data[$k]['apiurl']=$catUrl['catUrl'].$v['apiurl'];
            $data[$k]['request']=unserialize($v['request']);
            $data[$k]['response']=unserialize($v['response']);
        }



        /* 查询记录总数 */
        $count = M($name)->where($map)->count();

        //分页
        if($count > $row){
            $page = new \Think\Page($count, $row);
            $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
            $this->assign('_page', $page->show());
        }

        $this->assign('list_data', $data);
        $this->meta_title = 'API列表';
        $this->display();
    }



    /**
     * 列表
     *
     */
    public function lists ($p = 0){
        $page = intval($p);
        $page = $page ? $page : 1; //默认显示第一页数据

        $name = 'docapi';
        $row    =  100 ;
        $map=array('status'=>'1');
        $data = M($name)
            /* 查询指定字段，不指定则查询所有字段 */
            ->field(' * ')
            // 查询条件
            ->where($map)
            /* 默认通过id逆序排列 */
            ->order(' project_id ASC,id ASC')
            /* 数据分页 */
            ->page($page, $row)
            /* 执行查询 */
            ->select();
        foreach($data as $k=>$v){
            $catUrl=  $this->catUrl($v['project_id'],1);
            $data[$k]['apiurl']=$catUrl['catUrl'].$v['apiurl'];
            $data[$k]['request']=unserialize($v['request']);
            $data[$k]['response']=unserialize($v['response']);
        }



        /* 查询记录总数 */
        $count = M($name)->where($map)->count();

        //分页
        if($count > $row){
            $page = new \Think\Page($count, $row);
            $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
            $this->assign('_page', $page->show());
        }

        $this->assign('list_data', $data);
        $this->meta_title = 'API列表';
        $this->display();
    }

    public function add($model = 6){
        $name = 'docapi';
        if(IS_POST){
            $Model=M($name);
            $str=$Model->create();
            if(empty($str['title'])){
                $this->error('接口名称不能为空！');
            }
            if(empty($str['apiurl'])){
                $this->error('接口地址不能为空！');
            }
            $request=array_rotate($str['request']);
            $response=array_rotate($str['response']);
            $str['request']=serialize($request); //var_export($request,true);
            $str['response']=serialize($response);
            $str['edit_time']=time();
            $str['create_time']=time();
            $str['user_id']=UID;
            $str['status']=1;

            if($id = $Model->add($str)){
            // 记录日志
            api_log(UID,'添加接口 '.$str['title'],$id, 0, '');
            // 记录日志--
			    $this->cache_catlist(1);
                $this->success('添加'.$model['title'].'成功！',U('edit?id='.$id) );  //U('lists?model='.$model['name'])
            } else {
                $this->error($Model->getError());
            }
        } else {
            $this->assign('catlist', $this->catList());
            $this->meta_title = '新增'.$model['title'];
            $this->assign('post_action',  U('add'));
            $this->display($model['template_add']?$model['template_add']:'');
        }
    }

    //接口编辑本来是一个为了权限分成两个
    public function edit($id = 0){
        //var_dump(session('user_auth'));
        $id = intval($id);
        $map=array('id'=>$id);
        $name = 'docapi';
        $data = M($name)
            /* 查询指定字段，不指定则查询所有字段 */
            ->field(' * ')
            // 查询条件
            ->where($map)
            /* 执行查询 */
            ->find();


        $data['request']=unserialize($data['request']);
        $data['response']=unserialize($data['response']);
        $this->assign('catlist', $this->catList());
        $this->assign('data', $data);
        $this->assign('post_action',  U('save?id='.$id));
        $this->meta_title = '修改API接口';
        $this->display();

    }

    //接口保存本来是一个为了权限分成两个
    public function save($id = 0)
    {
        //var_dump(session('user_auth'));
        $id = intval($id);
        $map = array('id' => $id);
        $name = 'docapi';
        $data = M($name)
            /* 查询指定字段，不指定则查询所有字段 */
            ->field(' * ')
            // 查询条件
            ->where($map)
            /* 执行查询 */
            ->find();
        if (IS_POST) {
            $Model = M($name);
            $str = $Model->create();
            if(empty($str['title'])){
                $this->error('接口名称不能为空！');
            }
            if(empty($str['apiurl'])){
                $this->error('接口地址不能为空！');
            }
            $request = array_rotate($str['request']);
            $response = array_rotate($str['response']);
            $str['request'] = serialize($request); //var_export($request,true);
            $str['response'] = serialize($response);
            $str['edit_time'] = time();
            $str['user_id'] = UID;
            // 记录日志
            $log_data = $data; //
            unset($log_data['class_id']);
            unset($log_data['create_time']);
            unset($log_data['edit_time']);
            unset($log_data['status']);
            $log_diff = array_diff_assoc2_deep($log_data, $str);
            if (!empty($log_diff)) {
                api_log(UID, '修改接口', $id, 0, serialize($log_diff));
            }
            // 记录日志--
            if ($Model->save($str, $map)) {

                //记录行为
                action_log('update_apidoc', 'docapi', $id, session('user_auth.uid'), '修改Api文档');
                $this->cache_catlist(1);
                $this->success('修改成功！', U('edit?id=' . $id));
                exit;
            } else {
                $this->error($Model->getError());
            }
        }
        $this->error($Model->getError());
    }

    public function view($id=0)
    {
        $id = intval($id);
        $name = 'docapi';
        $map=array('status'=>'1','id'=>$id);
        $data = M($name)
            /* 查询指定字段，不指定则查询所有字段 */
            ->field(' * ')
            // 查询条件
            ->where($map)
            /* 执行查询 */
            ->find();
        $data['request']=unserialize($data['request']);
        $data['response']=unserialize($data['response']);
        $catUrl=$this->catUrl($data['project_id'],1);
        $data['apiurl']=$catUrl['catUrl'].$data['apiurl'];
        $log_list =  M('apilog')
            ->field(' * ')
            ->where(array('aid'=>$id,'cat_id'=>0))
            ->order('id DESC')
            /* 执行查询 */
            ->select();
        $this->assign('log_list', $log_list);
        $this->assign('data', $data);
        $this->meta_title = 'API查看';
        $this->display();
    }

    public function del($id=0)
    {
        $id = intval($id);
        $name = 'docapi';
        $map=array('id'=>$id);
        $del = M($name)
            /* 查询指定字段，不指定则查询所有字段 */
            // 查询条件
            ->where($map)
            /* 执行查询 */
            ->save(array('status'=>'0'));
        if($del===false){
            $this->error('删除失败');
        }else{
            $this->cache_catlist(1);
            $this->success('删除成功',U('/Docapi/Index'));
        }
    }




    protected function checkAttr($Model,$model_id){
        $fields     =   get_model_attribute($model_id,false);
        $validate   =   $auto   =   array();
        foreach($fields as $key=>$attr){
            if($attr['is_must']){// 必填字段
                $validate[]  =  array($attr['name'],'require',$attr['title'].'必须!');
            }
            // 自动验证规则
            if(!empty($attr['validate_rule'])) {
                $validate[]  =  array($attr['name'],$attr['validate_rule'],$attr['error_info']?$attr['error_info']:$attr['title'].'验证错误',0,$attr['validate_type'],$attr['validate_time']);
            }
            // 自动完成规则
            if(!empty($attr['auto_rule'])) {
                $auto[]  =  array($attr['name'],$attr['auto_rule'],$attr['auto_time'],$attr['auto_type']);
            }elseif('checkbox'==$attr['type']){ // 多选型
                $auto[] =   array($attr['name'],'arr2str',3,'function');
            }elseif('datetime' == $attr['type']){ // 日期型
                $auto[] =   array($attr['name'],'strtotime',3,'function');
            }
        }
        return $Model->validate($validate)->auto($auto);
    }

    //分类列表
    public function catlists($cid=0)
    {
        $show_catlist= getChildCat();
        $this->assign('show_catlist', $show_catlist);
        $this->assign('catlist', $this->catList());
        $this->display();

    }

    //分类列表添加
    public function catadd()
    {
        $parent_id=I('id');
        $name = 'categoryapi';
        if(IS_POST){
            $Model=M($name);
            $str=$Model->create();
            $str['parent_id']=I('project_id');
            $str['son_ids']='';
            if(empty($str['name'])){
                $this->error('分类名不能为空');
            }
            if(empty($str['category_url'])){
                $this->error('分类接口地址不能为空');
            }
            $str['status']=1;
            unset($str['project_id']);
            $new_id=$Model->add($str);
            // 记录日志
            api_log(UID,'添加分类', 0 , $new_id, '');
            // 记录日志--
            if($new_id){
                // 更新父栏目son——ids
                if(!empty($str['parent_id'])){
                    $map=array('parent_id'=>$str['parent_id'],'status'=>1);
                    $cat_sons = M($name)
                        /* 查询指定字段，不指定则查询所有字段 */
                        ->field(' id ')
                        // 查询条件
                        ->where($map)
                        /* 执行查询 */
                        ->select();
                    $son_ids = '';
                    foreach($cat_sons as $k=>$v){
                        if(empty($son_ids)){
                            $son_ids=$v['id'];
                        }else{
                            $son_ids=$son_ids.','.$v['id'];
                        }
                    }

                    M($name)->where(array('id'=>$str['parent_id']))->save(array('son_ids'=>$son_ids));
                }
				$this->cache_catlist(1);
                $this->success('添加'.$model['title'].'成功！', U('catlists?model='.$model['name']));
            } else {
                $this->error($Model->getError());
            }
        } else {
            $this->assign('parent_id', $parent_id);
            $this->assign('catlist', $this->catList());
            $this->meta_title = '新增';
            $this->display('catedit');
        }
    }


    /**
     * 分类列表修改
     */
    public function catedit()
    {
        $id=I('get.id');
        if(empty($id)){
            $this->error('修改分类ID不能为空');
        }
        $name = 'categoryapi';
        $map=array('id'=>$id);
        $cat_data = M($name)
            /* 查询指定字段，不指定则查询所有字段 */
            ->field(' * ')
            // 查询条件
            ->where($map)
            /* 执行查询 */
            ->find();
        

        $this->assign('data', $cat_data);
        $this->assign('catlist', $this->catList());
        $this->assign('post_action',  U('catsave?id='.$id));
        $this->meta_title = '新增';
        $this->display('catedit');

    }

    //分类修改保存
    public function catsave()
    {
        $id = I('get.id');
        if (empty($id)) {
            $this->error('修改分类ID不能为空');
        }
        $name = 'categoryapi';
        $map = array('id' => $id);
        $cat_data = M($name)
            /* 查询指定字段，不指定则查询所有字段 */
            ->field(' * ')
            // 查询条件
            ->where($map)
            /* 执行查询 */
            ->find();

        if (IS_POST) {
            $Model = M($name);
            $str = $Model->create();
            $str['parent_id'] = I('project_id');
            $str['son_ids'] = '';
            if (empty($str['name'])) {
                $this->error('分类名不能为空');
            }
            if (empty($str['category_url'])) {
                $this->error('分类接口地址不能为空');
            }
            $str['status'] = 1;


            // 记录日志
            $log_data = $cat_data; //
            $log_diff = array_diff_assoc2_deep($log_data, $str);
            if (!empty($log_diff)) {
                api_log(UID, '修改分类', 0, $id, serialize($log_diff));
            }
            // 记录日志--
            unset($str['project_id']);
            $new_id = $Model->where(array('id' => $id))->save($str);
            if ($new_id !== false) {
                // 更新父栏目son——ids
                if (!empty($str['parent_id'])) {
                    $map = array('parent_id' => $str['parent_id'], 'status' => 1);
                    $cat_sons = M($name)
                        /* 查询指定字段，不指定则查询所有字段 */
                        ->field(' id ')
                        // 查询条件
                        ->where($map)
                        /* 执行查询 */
                        ->select();
                    $son_ids = '';
                    foreach ($cat_sons as $k => $v) {
                        if (empty($son_ids)) {
                            $son_ids = $v['id'];
                        } else {
                            $son_ids = $son_ids . ',' . $v['id'];
                        }
                    }

                    M($name)->where(array('id' => $str['parent_id']))->save(array('son_ids' => $son_ids));
                }
                $this->cache_catlist(1);
                $this->success('修改' . $model['title'] . '成功！', U('catlists?model=' . $model['name']));
                exit;
            } else {
                $this->error($Model->getError());
            }
        }
        $this->error($Model->getError());
    }

        public function catDel(){
        $id=I('id');
        $name = 'categoryapi';
        $map=array('id'=>$id);
        $result = M($name)
            /* 查询指定字段，不指定则查询所有字段 */
            // 查询条件
            ->where($map)
            /* 执行查询 */
            ->save(array('status'=>0));
        if ($result!==false) {
            $this->success('删除成功');
        } else {
            $this->error($Model->getError());
        }
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
            // echo 'Refresh cache success';
            $this->success('Refresh cache success');
        }else{
            return true;
        }

    }

    public function ajxList($cid=0)
    {
        $cid = intval($cid);
        $name = 'docapi';
        $map=array('status'=>1,'project_id'=>$cid);
        $list = M($name)
            /* 查询指定字段，不指定则查询所有字段 */
            ->field(' id,title ')
            // 查询条件
            ->where($map)
            /* 执行查询 */
            ->select();

        if($list){
            $rs=array(
                'status'=>'1',
                'list'=>$list,
                'msg'=>'请求成功'
            );
        }else{
            $rs=array(
                'status'=>'0',
                'msg'=>'请求失败'
            );
        }
        echo json_encode($rs);
        exit;

    }

    //返回分类的url
    public function catUrl($cid=0,$type=0)
    {
        $cid = intval($cid);
        if(empty($cid)){
            $rs=array(
                'status'=>'0',
                'msg'=>'没有数据',
                );
            goto end;
        }
        $name = 'categoryapi';
        $map=array('status'=>1);
        $cat1 = M($name)
            /* 查询指定字段，不指定则查询所有字段 */
            ->field(' * ')
            // 查询条件
            ->where($map)
            /* 执行查询 */
            ->select();
        $arr=array();
        foreach($cat1 as $k=>$v){
            $arr[$v['id']]=$v['parent_id'];
        }

        $catUrl = '';
        foreach($cat1 as $k=>$v){
            if($v['id']==$cid){
                $catUrl = $v['category_url'].'/'.$catUrl;
                break;
            }
        }

        while($arr[$cid]) {
            $cid = $arr[$cid];
            foreach($cat1 as $k=>$v){
                if($v['id']==$cid){
                    $catUrl = ''.$v['category_url'].'/'.$catUrl;
                    break;
                }

            }
        }


        $rs=array(
            'status'=>'1',
            'catUrl'=>$catUrl,
            'msg'=>'请求成功'
        );
        end:
        if($type==1){
            return $rs;
        }else{
            echo json_encode($rs);
        }


    }

    //返回分类的url
    public function catUrls($cid=0,$type=0)
    {
        $cid = intval($cid);
        if(empty($cid)){
            $rs=array(
                'status'=>'0',
                'msg'=>'没有数据',
            );
            goto end;
        }
        $name = 'categoryapi';
        $map=array('id'=>$cid,'status'=>1);
        $cat1 = M($name)
            /* 查询指定字段，不指定则查询所有字段 */
            ->field(' * ')
            // 查询条件
            ->where($map)
            /* 执行查询 */
            ->find();


        $catUrl =$cat1['category_url'];

        $rs=array(
            'status'=>'1',
            'catUrl'=>$catUrl,
            'msg'=>'请求成功'
        );
        end:
        if($type==1){
            return $rs;
        }else{
            echo json_encode($rs);
        }


    }

    /**
     * 返回分类列表
     * @param int $cid  分类ID 顶级分类为0
     * @return array
     */
    public function catAjax($cid = 0)
    {
        $cid = intval($cid);
        if ($cid == '') {
            $cid = 0;
        }
        $name = 'categoryapi';
        if ($cid == 0) {
            $map = array('parent_id' => 0,'status' => 1);
        } else {
            $map = array('parent_id' => $cid, 'status' => 1);
        }

        $catlist = M($name)
            /* 查询指定字段，不指定则查询所有字段 */
            ->field(' id,name,parent_id,category_url ')
            // 查询条件
            ->where($map)
            /* 执行查询 */
            ->select();

        if ($catlist) {
            $rs = array(
                'status' => '1',
                'data' => $catlist,
                'msg' => '请求成功'
            );

        } else {
            $rs = array(
                'status' => '0',
                'msg' => '请求失败'
            );
        }

        echo json_encode($rs);

    }




    /**
     * 接口生成word文档
     */

    public function outWord(){
        $page = intval($p);
        $page = $page ? $page : 1; //默认显示第一页数据

        $name = 'docapi';
        $row    =  10 ;
        $map=array('status'=>'1');
        $data = M($name)
            /* 查询指定字段，不指定则查询所有字段 */
            ->field(' * ')
            // 查询条件
            ->where($map)
            /* 默认通过id逆序排列 */
            ->order(' project_id ASC,id ASC')
            /* 数据分页 */
            ->page($page, $row)
            /* 执行查询 */
            ->select();
        foreach($data as $k=>$v){
            $catUrl=  $this->catUrl($v['project_id'],1);
            $data[$k]['apiurl']=$catUrl['catUrl'].$v['apiurl'];
            $data[$k]['request']=unserialize($v['request']);
            $data[$k]['response']=unserialize($v['response']);
        }

        /* 查询记录总数 */
        $count = M($name)->where($map)->count();

        //分页
        if($count > $row){
            $page = new \Think\Page($count, $row);
            $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
            $this->assign('_page', $page->show());
        }

        $this->assign('list_data', $data);
        $this->meta_title = 'API列表';

        $content = $this->fetch('outword');//获取模板内容信息word是模板的名称

        //$content = file_get_contents('http://doc.rl/index.php?s=/Docapi/Opapi/index.html');
        $fileContent = $this->WordMake($content);//生成word内容
        //echo $fileContent;
        //exit;
        //$name = iconv("utf-8", "GBK",$data['username']);//转换好生成的word文件名编码
        $date = date('Y-m-d-His', time());
        $filename = $date.'.doc';
        $my_file = 'Runtime/'.$filename;
        $fp = fopen( $my_file, 'w') or die('Cannot open file: '.$my_file);//打开生成的文档
        fwrite($fp, $fileContent);//写入包保存文件
        Header( "Content-type:  application/octet-stream ");
        Header( "Accept-Ranges:  bytes ");
        Header( "Content-Disposition:  attachment;  filename= $filename");
        echo $fileContent;
        fclose($fp);

    }

    /**
     * 根据HTML代码获取word文档内容
     * @param string $content HTML内容
     * @param string $absolutePath 网页的绝对路径。如果HTML内容里的图片路径为相对路径，那么就需要填写这个参数，来让该函数自动填补成绝对路径。这个参数最后需要以/结束
     * @param bool $isEraseLink 是否去掉HTML内容中的链接
     */
    private function WordMake( $content , $absolutePath = "" , $isEraseLink = true )
    {
        vendor('Wordmaker');
        $mht = new \Wordmaker();
        if ($isEraseLink){
            $content = preg_replace('/<a\s*.*?\s*>(\s*.*?\s*)<\/a>/i' , '$1' , $content);   //去掉链接
            $content = preg_replace('/<link\s*.*?\s*>/i' , '$1' , $content);   //去掉链接
            $content = preg_replace('/<script\s*.*?\s*>*<\/script>/i' , '$1' , $content);   //去掉链接
        }
        //echo $content;

        $images = array();
        $files = array();
        $matches = array();
        //这个算法要求src后的属性值必须使用引号括起来
        if ( preg_match_all('/<img[.\n]*?src\s*?=\s*?[\"\'](.*?)[\"\'](.*?)\/>/i',$content ,$matches ) ){
            $arrPath = $matches[1];
            for ( $i=0;$i<count($arrPath);$i++)
            {
                $path = $arrPath[$i];
                $imgPath = trim( $path );
                if ( $imgPath != "" )
                {
                    $files[] = $imgPath;
                    if( substr($imgPath,0,7) == 'http://')
                    {
//绝对链接，不加前缀
                    }
                    else
                    {
                        $imgPath = $absolutePath.$imgPath;
                    }
                    $images[] = $imgPath;
                }
            }
        }
        $mht->AddContents("tmp.html",$mht->GetMimeType("tmp.html"),$content);
        for ( $i=0;$i<count($images);$i++)
        {
            $image = $images[$i];
            if ( @fopen($image , 'r') )
            {
                $imgcontent = @file_get_contents( $image );
                if ( $content )
                    $mht->AddContents($files[$i],$mht->GetMimeType($image),$imgcontent);
            }
            else
            {
                echo "file:".$image." not exist!<br />";
            }
        }
        return $mht->GetFile();
    }
}