<?php /*a:2:{s:83:"/home/www/web/thinkphp5.1/mch/application/index_admin/view/goods_category/edit.html";i:1551064593;s:27:"template/admin_pc/base.html";i:1551077175;}*/ ?>
<!DOCTYPE HTML>
<html><head>
    
<link type="text/css" rel="stylesheet" href="public_admin_common_css/fenlei.css" />
<link type="text/css" rel="stylesheet" href="public_admin_common_css/skin.css" />

    <link type="text/css" rel="stylesheet" href="http://mch.meishangyun.com/static/index_admin/css/fenlei.css" />
    <link type="text/css" rel="stylesheet" href="http://mch.meishangyun.com/static/index_admin/css/skin.css" />
    <!--前置自定义js-->
    
    <script type="text/javascript" src="http://mch.meishangyun.com/static/common/js/jquery/jquery-1.9.1.min.js"></script>
</head><body>

    <div class="page">
        <div class="fixed-bar">
            <div class="item-title">
                <ul class="tab-base">
                    <li><a href="<?php echo url('manage'); ?>"><span>管理</span></a></li>
                    <li><a href="<?php echo url('edit'); ?>" class="current"><span>编辑</span></a></li>
                </ul>
            </div>
        </div>
        <div class="fixed-empty"></div>
        <div align=center style="margin-top:15px;">
            <table id=table5 border=0 cellspacing=0 cellpadding=0 width="100%" class="edit_common_item">
                <tbody class="edit_common_tr">
                    <tr>
                        <td class="box_top box_border" height=35 align=center>
                            <?php if($operate == 'addLower'): ?>
                                新增
                                <?php switch($info['level']): case "1": ?>二级<?php break; case "2": ?>三级<?php break; endswitch; ?>
                                分类
                            <?php else: if(empty($info) || (($info instanceof \think\Collection || $info instanceof \think\Paginator ) && $info->isEmpty())): ?>
                                    新增一级分类
                                <?php else: ?>
                                    修改
                                    <?php switch($info['level']): case "1": ?>一级<?php break; case "2": ?>二级<?php break; case "3": ?>三级<?php break; endswitch; ?>
                                    分类
                                <?php endif; endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <td valign=middle width="100%">
                            <div align=center class="goods_type_catague">
                                <form id="form1">
                                    <?php if($operate == 'addLower'): ?><!-- 新增下级 -->
                                        <input type="hidden" name="id" value="">
                                        <?php if(($info['level'] == 1) or ($info['level'] == 2)): ?>
                                            <div class="goods_c_item">
                                                <span class='goods_label_title' height=40 width="11%" align=right>所属分类：</span>
                                                <div width="89%" align=left>
                                                    <?php if($info['level'] == 1): ?>
                                                        <select name="parent_id_1" class="small form-control">
                                                            <option value="0" >一级分类</option>
                                                            <?php if(is_array($allCategoryList) || $allCategoryList instanceof \think\Collection || $allCategoryList instanceof \think\Paginator): $i = 0; $__LIST__ = $allCategoryList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;if($vo['level'] == 1): if($vo['id'] == $info['id']): ?>
                                                                        <option value="<?php echo htmlentities($vo['id']); ?>" selected="selected"><?php echo htmlentities($vo['name']); ?></option>
                                                                        <?php else: ?>
                                                                        <option value="<?php echo htmlentities($vo['id']); ?>"><?php echo htmlentities($vo['name']); ?></option>
                                                                    <?php endif; endif; endforeach; endif; else: echo "" ;endif; ?>
                                                        </select>
                                                    <?php else: ?>
                                                        <select name="parent_id_1" class="small form-control">
                                                            <option value="0" >一级分类</option>
                                                            <?php if(is_array($allCategoryList) || $allCategoryList instanceof \think\Collection || $allCategoryList instanceof \think\Paginator): $i = 0; $__LIST__ = $allCategoryList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;if($vo['level'] == 1): if($vo['id'] == $info['parent_id_1']): ?>
                                                                        <option value="<?php echo htmlentities($vo['id']); ?>" selected="selected"><?php echo htmlentities($vo['name']); ?></option>
                                                                    <?php else: ?>
                                                                        <option value="<?php echo htmlentities($vo['id']); ?>"><?php echo htmlentities($vo['name']); ?></option>
                                                                    <?php endif; endif; endforeach; endif; else: echo "" ;endif; ?>
                                                        </select>
                                                        <select name="parent_id_2" class="small form-control">
                                                            <option value="0" >二级分类</option>
                                                            <?php if(is_array($allCategoryList) || $allCategoryList instanceof \think\Collection || $allCategoryList instanceof \think\Paginator): $i = 0; $__LIST__ = $allCategoryList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;if($vo['level'] == 2): if($vo['id'] == $info['id']): ?>
                                                                        <option value="<?php echo htmlentities($vo['id']); ?>" class="<?php echo htmlentities($vo['parent_id_1']); ?>" selected="selected"><?php echo htmlentities($vo['name']); ?></option>
                                                                    <?php else: ?>
                                                                        <option value="<?php echo htmlentities($vo['id']); ?>" class="<?php echo htmlentities($vo['parent_id_1']); ?>"><?php echo htmlentities($vo['name']); ?></option>
                                                                    <?php endif; endif; endforeach; endif; else: echo "" ;endif; ?>
                                                        </select>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                        <div class="goods_c_item">
                                            <span class='goods_label_title' height=40 width="11%" align=right>分类名称：</span>
                                            <div class= width="89%" align=left>
                                                <input class=inputboxadmin name="name" value="">
                                            </div>
                                        </div>
                                        <div class="goods_c_item">
                                            <span class='goods_label_title' height=40 width="11%" align=right>分类说明：</span>
                                            <div class= width="89%" align=left>
                                                <textarea class="project_description" name="remark" ></textarea>
                                            </div>
                                        </div>
                                    <div class="goods_c_item">
                                        <span class='goods_label_title' height=40 width="11%" align=right>分类简介：</span>
                                        <div class= width="89%" align=left>
                                            <textarea class="project_description" name="intro" ></textarea>
                                        </div>

                                    </div>
                                    <div class="goods_c_item">
                                        <span class='goods_label_title' height=40 width="11%" align=right>分类标签：</span>
                                        <div class= width="89%" align=left>
                                            <textarea class="project_description" name="tag" ><?php echo htmlentities($info['tag']); ?></textarea>
                                        </div>
                                        多个标签请用逗号分隔，例如：正品保障,纳晶水光
                                    </div>
                                        <div class="goods_c_item">
                                            <span class='goods_label_title' height=40 width="11%" align=right>分类图片：</span>
                                            <div class="type_img_box" width="89%" >
                                                <input type="file" class="upload">
                                                <label class="uploadbtn" title="JPG,GIF,PNG"></label>
                                                <p class="showimg" id="showimg">图片上传大小请小于50k</p>
                                                <div class="upload_img_box">
                                                    <img  class="img" src="http://mch.meishangyun.com/static/common/img/default/goods_default.png">
                                                    <input type="hidden" value="" name="img"/>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="goods_c_item">
                                            <span class='goods_label_title' height=40 width="11%" align=right>排序：</span>
                                            <div width="89%" align=left>
                                                <input class=inputboxadmin name="sort" value="<?php echo htmlentities($info['sort']); ?>">
                                            </div>
                                        </div>
                                    <?php else: ?><!-- 编辑本级 -->
                                        <input type="hidden" name="id" value="<?php echo htmlentities($info['id']); ?>">
                                        <?php if(($info['level'] == 2) or ($info['level'] == 3)): ?>
                                            <div class="goods_c_item">
                                                <span class='goods_label_title' height=40 width="11%" align=right>所属分类：</span>
                                                <div width="89%" align=left>
                                                    <?php if(($info['level'] == 2) or ($info['level'] == 3)): ?>
                                                        <select name="parent_id_1" class="small form-control">
                                                            <option value="0" >一级分类</option>
                                                            <?php if(is_array($allCategoryList) || $allCategoryList instanceof \think\Collection || $allCategoryList instanceof \think\Paginator): $i = 0; $__LIST__ = $allCategoryList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;if($vo['level'] == 1): if($vo['id'] == $info['parent_id_1']): ?>
                                                                        <option value="<?php echo htmlentities($vo['id']); ?>" selected="selected"><?php echo htmlentities($vo['name']); ?></option>
                                                                        <else/>
                                                                        <option value="<?php echo htmlentities($vo['id']); ?>"><?php echo htmlentities($vo['name']); ?></option>
                                                                    <?php endif; endif; endforeach; endif; else: echo "" ;endif; ?>
                                                        </select>
                                                    <?php endif; if(($info['level'] == 3)): ?>
                                                        <select name="parent_id_2" class="small form-control">
                                                            <option value="0" >二级分类</option>
                                                            <?php if(is_array($allCategoryList) || $allCategoryList instanceof \think\Collection || $allCategoryList instanceof \think\Paginator): $i = 0; $__LIST__ = $allCategoryList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;if($vo['level'] == 2): if($vo['id'] == $info['parent_id_2']): ?>
                                                                        <option value="<?php echo htmlentities($vo['id']); ?>" class="<?php echo htmlentities($vo['parent_id_1']); ?>" selected="selected"><?php echo htmlentities($vo['name']); ?></option>
                                                                        <?php else: ?>
                                                                        <option value="<?php echo htmlentities($vo['id']); ?>" class="<?php echo htmlentities($vo['parent_id_1']); ?>"><?php echo htmlentities($vo['name']); ?></option>
                                                                    <?php endif; endif; endforeach; endif; else: echo "" ;endif; ?>
                                                        </select>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                        <div class="goods_c_item">
                                            <span class='goods_label_title' height=40 width="11%" align=right>分类名称：</span>
                                            <div class= width="89%" align=left>
                                                <input class=inputboxadmin name="name" value="<?php echo htmlentities($info['name']); ?>">
                                            </div>
                                        </div>
                                        <div class="goods_c_item">
                                            <span class='goods_label_title' height=40 width="11%" align=right>分类说明：</span>
                                            <div class= width="89%" align=left>
                                                <textarea class="project_description" name="remark" ><?php echo htmlentities($info['remark']); ?></textarea>
                                            </div>
                                        </div>
                                    <div class="goods_c_item">
                                        <span class='goods_label_title' height=40 width="11%" align=right>分类简介：</span>
                                        <div class= width="89%" align=left>
                                            <textarea class="project_description" name="intro" ><?php echo htmlentities($info['intro']); ?></textarea>
                                        </div>

                                    </div>
                                    <div class="goods_c_item">
                                        <span class='goods_label_title' height=40 width="11%" align=right>分类标签：</span>
                                        <div class= width="89%" align=left>
                                            <textarea class="project_description" name="tag" ><?php echo htmlentities($info['tag']); ?></textarea>
                                        </div>
                                        多个标签请用逗号分隔，例如：正品保障,纳晶水光
                                    </div>
                                        <div class="goods_c_item">
                                            <span class='goods_label_title' height=40 width="11%" align=right>分类图片：</span>
                                            <div class="type_img_box" width="89%" >
                                                <input type="file" class="upload">
                                                <label class="uploadbtn" title="JPG,GIF,PNG"></label>
                                                <p class="showimg" id="showimg">图片上传大小请小于50k</p>
                                                <div class="upload_img_box">
                                                    <?php if(empty($info['img']) || (($info['img'] instanceof \think\Collection || $info['img'] instanceof \think\Paginator ) && $info['img']->isEmpty())): ?>
                                                    <img  class="img" src="http://mch.meishangyun.com/static/common/img/default/goods_default.png">
                                                    <?php else: ?>
                                                    <img class="img" src="http://mch.meishangyun.com/uploads/<?php echo htmlentities($info['img']); ?>">
                                                    <?php endif; ?>
                                                    <input type="hidden" value="<?php echo htmlentities($info['img']); ?>" name="img"/>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="goods_c_item">
                                            <span class='goods_label_title' height=40 width="11%" align=right>排序：</span>
                                            <div width="89%" align=left>
                                                <input class=inputboxadmin name="sort" value="<?php echo htmlentities($info['sort']); ?>">
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td height=60 width="100%" align=center>
                            <input type="button" class="button_save_black_4" value="确定">
                            <input type="button" class="button_save_black" value="返回">
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <!-- 隐藏区 -->
    <div style="display:none">
        <select id="parent_id_2_tmp">
            <option value="0" >二级分类</option>
            <?php if(is_array($allCategoryList) || $allCategoryList instanceof \think\Collection || $allCategoryList instanceof \think\Paginator): $i = 0; $__LIST__ = $allCategoryList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;if($vo['level'] == 2): ?>
                    <option value="<?php echo htmlentities($vo['id']); ?>" class="<?php echo htmlentities($vo['parent_id_1']); ?>"><?php echo htmlentities($vo['name']); ?></option>
                <?php endif; endforeach; endif; else: echo "" ;endif; ?>
        </select>
    </div>


<script type="text/javascript" src="http://mch.meishangyun.com/static/common/js/public.js"></script>
<script type="text/javascript" src="http://mch.meishangyun.com/static/common/js/common.js"></script>
<script type="text/javascript" src="http://mch.meishangyun.com/static/h-ui.lib/layer/2.4/layer.js"></script>
<script type="text/javascript" src="http://mch.meishangyun.com/static/common_admin/js/dialog.js"></script>
<script type="text/javascript" src="http://mch.meishangyun.com/static/common_admin/js/paging.js"></script>
<!--自定义js-->

    <script type="text/javascript">
        $(function(){
            //确定
            $(".button_save_black_4").click(function(){
                var postData = $('#form1').serializeObject();
                postData.level = 1;
                if(postData.parent_id_1){
                    postData.level = 2;
                }
                if(postData.parent_id_2){
                    postData.level = 3;
                }
                var url = controller + 'edit';
                $.post(url,postData,function(msg){
                    dialog.msg(msg,'',function () {
                        location.href= window.history.go(-1);
                    });
                });
            });

            //一级、二级-联动
            $('body').on('change','[name=parent_id_1]',function(){
                var parent_id_1 = $(this).val();
                var newOption = $('#parent_id_2_tmp').find('option[value=0],[class='+parent_id_1+']').clone();
                $('[name=parent_id_2]').html(newOption);
            });

            //返回
            $('.button_save_black').click(function () {
                location.href = controller + 'manage';
            });

            // 选择图片
            $('.upload').on('change',function(){
                var img = event.target.files[0];
                // 判断是否图片
                if(!img){
                    return false;
                }
                // 判断图片格式
                if(!(img.type.indexOf('image')==0 && img.type && /\.(?:jpg|png|gif|jpeg)$/.test(img.name)) ){
                    alert('图片只能是jpg,jpeg,gif,png');
                    return false;
                }
                var reader = new FileReader();
                reader.readAsDataURL(img);
                reader.onload = function(e){
                    $(".img").attr("src",e.target.result);
                    var postData = {fileBase64: e.target.result};
                    var url = controller + 'uploadFileToTemp';
                    //提交
                    $.post(url,postData,function(msg){
                        $(".img").attr("src",'/uploads/' + msg.info);
                        $("[name=img]").val(msg.info);
                    });
                }
            });
        });
    </script>

<script type="text/javascript">
    var domain = '<?php echo request()->domain(); ?>'+'/'+'index.php/';
    var module = domain + '<?php echo request()->module(); ?>/';
    var controller = module + '<?php echo request()->controller(); ?>/';
    var action = controller + '<?php echo request()->action(); ?>';
    var uploads = '<?php echo request()->domain(); ?>'+'/uploads/';
    $(function(){
    });
</script>
</body></html>