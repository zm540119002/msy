<?php /*a:2:{s:74:"/home/www/web/thinkphp5.1/mch/application/index_admin/view/scene/edit.html";i:1551064593;s:27:"template/admin_pc/base.html";i:1551077175;}*/ ?>
<!DOCTYPE HTML>
<html><head>
    
    <link type="text/css" rel="stylesheet" href="http://mch.meishangyun.com/static/index_admin/css/fenlei.css" />
    <link type="text/css" rel="stylesheet" href="http://mch.meishangyun.com/static/index_admin/css/skin.css" />
    <!--前置自定义js-->
    
    <script type="text/javascript" src="http://mch.meishangyun.com/static/common/js/jquery/jquery-1.9.1.min.js"></script>
</head><body>

    <div class="page">
        <div class="fixed-bar">
            <div class="item-title">
                <ul class="tab-base">
                    <li><a href="<?php echo url('manage'); ?>" ><span>管理</span></a></li>
                    <li><a href="<?php echo url('edit'); ?>" class="current"><span>编辑</span></a></li>
                </ul>
            </div>
        </div>
        <div style="margin-top:15px;">
            <form id="form1">
                <input type="hidden" name="id" value="<?php echo htmlentities($info['id']); ?>">
                <table id=table18 class="add_new_merchandise" border=0 cellspacing=1 width="100%" height=57>
                    <tbody>
                    <tr>
                        <td class=dotted_bottom_gray height=40 width="11%" align=right>项目名称：</td>
                        <td class=dotted_bottom_gray width="89%" align=left>
                            <div class= width="89%" align=left>
                                <textarea class="project_description" name="name" ><?php echo htmlentities($info['name']); ?></textarea>
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <td class=dotted_bottom_gray height=40 width="11%" align=right>排序：</td>
                        <td class=dotted_bottom_gray width="89%" align=left>
                            <input style="width: 100px; " class="inputboxadmin" name="sort" value="<?php echo htmlentities($info['sort']); ?>">
                        </td>
                    </tr>

                    <tr>
                        <td class=dotted_bottom_gray height=40 width="11%" align=right>项目简介：</td>
                        <td class=dotted_bottom_gray width="89%" align=left>
                            <div class= width="89%" align=left>
                                <textarea class="project_description" name="intro" ><?php echo htmlentities($info['intro']); ?></textarea>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class=dotted_bottom_gray height=40 width="11%" align=right>标签：</td>
                        <td class=dotted_bottom_gray width="89%" align=left>
                            <input style="width: 200px; " class="inputboxadmin" name="tag" value="<?php echo htmlentities($info['tag']); ?>">
                            多个标签请用逗号分隔，例如：正品保障,纳晶水光
                        </td>
                    </tr>
                    <tr>
                        <td class=dotted_bottom_gray height=40 width="11%" align=right>缩略图：</td>
                        <td class=dotted_bottom_gray width="89%" align=left>
                            <p class="f20 picture-tipc-box"><span class="friend-tipc">*注意</span>：必须上传图片(格式为jpg、jpeg、png、gif)</p>
                                <div class="upload-picture-module f24">
                                    <div>
                                        <div class="picture-module">
                                            <input type="file" class="uploadImg uploadSingleImg" name="">
                                             <?php if(empty($info['thumb_img']) || (($info['thumb_img'] instanceof \think\Collection || $info['thumb_img'] instanceof \think\Paginator ) && $info['thumb_img']->isEmpty())): ?>
                                            <img class="upload_img" src="">
                                            <?php else: ?>
                                            <img class="upload_img " src="http://mch.meishangyun.com/uploads/<?php echo htmlentities($info['thumb_img']); ?>" />
                                            <?php endif; ?>
                                            <input type="hidden" class="layer-thumbnail-picture img"  value="<?php echo htmlentities($info['thumb_img']); ?>" name="thumb_img"/>
                                        </div>
                                        <!--<p>企业营业执照</p>-->
                                    </div>
                                </div>
                        </td>
                    </tr>
                    <tr>
                        <td class=dotted_bottom_gray height=40 width="11%" align=right>背景颜色：</td>
                        <td class=dotted_bottom_gray width="89%" align=left>
                            <p class="f20 picture-tipc-box"><span class="friend-tipc">*注意</span>：必须上传图片(格式为jpg、jpeg、png、gif)</p>
                            <div class="upload-picture-module f24">
                                <div>
                                    <div class="picture-module">
                                        <input type="file" class="uploadImg uploadSingleImg" name="">
                                        <?php if(empty($info['thumb_img']) || (($info['thumb_img'] instanceof \think\Collection || $info['thumb_img'] instanceof \think\Paginator ) && $info['thumb_img']->isEmpty())): ?>
                                        <img class="upload_img" src="">
                                        <?php else: ?>
                                        <img class="upload_img " src="http://mch.meishangyun.com/uploads/<?php echo htmlentities($info['background_img']); ?>" />
                                        <?php endif; ?>
                                        <input type="hidden" class="layer-thumbnail-picture img"  value="<?php echo htmlentities($info['background_img']); ?>" name="background_img"/>
                                    </div>
                                    <!--<p>企业营业执照</p>-->
                                </div>
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <td class=dotted_bottom_gray height=40 width="11%" align=right>项目主图:</td>
                        <td class=dotted_bottom_gray width="89%" align=left>
                            <!-- 加载编辑器的容器 -->
                            <script id="main_img" type="text/plain">
                                
                            </script>
                            <input id="mainImg" type="hidden" value="<?php echo htmlentities(formatImg($info['main_img'])); ?>">
                        </td>
                    </tr>
                    <tr>
                        <td width="11%"></td>
                        <td height=60 width="50%" align=center>
                            <a href='javascript:void(0);'><input class="button_save_black_4" value="确定" type="button"></a>
                            &nbsp;&nbsp;
                            <input class="button_save_black" name="add0" value="返回" type="button">
                        </td>
                    </tr>
                    </tbody>
                </table>
            </form>
        </div>
    </div>


<footer class="f24">
    <a href="http://www.miitbeian.gov.cn">
        <span class="icp_icon">Copyright@广东维雅生物科技有限公司</span>
        <span class="icp_icon">粤ICP备18130945号-1</span>
    </a>
</footer>

<script type="text/javascript" src="http://mch.meishangyun.com/static/common/js/public.js"></script>
<script type="text/javascript" src="http://mch.meishangyun.com/static/common/js/common.js"></script>
<script type="text/javascript" src="http://mch.meishangyun.com/static/h-ui.lib/layer/2.4/layer.js"></script>
<script type="text/javascript" src="http://mch.meishangyun.com/static/common_admin/js/dialog.js"></script>
<script type="text/javascript" src="http://mch.meishangyun.com/static/common_admin/js/paging.js"></script>
<!--自定义js-->

<!-- 配置文件 -->
<script type="text/javascript" src="http://mch.meishangyun.com/static/h-ui.lib/Ueditor/ueditor.config.js"></script>
<!-- 编辑器源码文件 -->
<script type="text/javascript" src="http://mch.meishangyun.com/static/h-ui.lib/Ueditor/ueditor.all.js"></script>
<script type="text/javascript" charset="utf-8" src="http://mch.meishangyun.com/static/h-ui.lib/Ueditor/lang/zh-cn/zh-cn.js"></script>
<script src="https://cdn.bootcss.com/html5media/1.1.8/html5media.min.js"></script>
<!-- 实例化编辑器 -->
<script type="text/javascript">
    var main_img = UE.getEditor('main_img', {
        toolbars: [
            ['imagenone', 'imageleft', 'imageright', 'imagecenter', 'simpleupload', 'insertimage']
        ],
        autoHeightEnabled: true,
        autoFloatEnabled: true
    });
</script>
    <script type="text/javascript">
        $(function(){
            //项目主图
            main_img.ready(function() {
                main_img.setContent($('#mainImg').val());
            });
            //确定
            $('.button_save_black_4').on('click',function(){
                var postData = $('#form1').serializeObject();
                //主图
                postData.main_img = '';
                $.each($('#main_img').find('iframe').contents().find('.view img'),function(){
                    postData.main_img += $(this).attr('src') + ',';

                });

                var url = module +'Scene/edit';
                $.post(url,postData,function(msg){
                    dialog.msg(msg,'',function () {
                        location.href = controller + 'manage';
                    });
                });
            });
            // 选择单图片
            $('body').on('change','.uploadSingleImg',function () {
                var _this=$(this);
                uploadsSingleImgFlag = false;
                var img = event.target.files[0];
                var uploadfileSize=img.size/1024/1024;
                var obj=$(this).parent();
                // 判断是否图片
                if(!img){
                    return false;
                }
                // 判断图片格式
                var imgRegExp=/\.(?:jpg|jpeg|png|gif)$/;
                if(!(img.type.indexOf('image')==0 && img.type && imgRegExp.test(img.name)) ){
                    dialog.error('请上传：jpg、jpeg、png、gif格式图片');
                    return false;
                }
                if(uploadfileSize>1){
                    dialog.error('图片大小不能超过1M');
                    return false;
                }
                var reader = new FileReader();
                reader.readAsDataURL(img);
                reader.onload = function(e){
                    var imgUrl=e.target.result;
                    $(obj).addClass('active');
                    var postData = {fileBase64: e.target.result};
                    // postData.imgWidth = 145;
                    // postData.imgHeight = 100;
                    $(obj).find('img').attr('src',imgUrl);
                    var type = _this.data('type');
                    if(type == 'notupload'){
                        $(obj).find('.img').val(imgUrl);
                        console.log(1);
                        return false;
                    }
                    //提交
                    $.post(controller+"uploadFileToTemp",postData,function(msg){
                        if(msg.status == 1){
                            uploadsSingleImgFlag = true;
                            $(obj).find('.img').val(msg.info);
                        }else{
                            dialog.error(msg.info)
                        }
                    })
                }
            });
            //返回
            $('.button_save_black').click(function(){
                location.href = controller + '/manage';
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