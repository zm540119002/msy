<?php /*a:3:{s:67:"D:\web\thinkphp5.1\mch\application/index_admin/view\goods\edit.html";i:1551065534;s:27:"template/admin_pc/base.html";i:1551083825;s:83:"D:\web\thinkphp5.1\mch\application/index_admin/view\goods_category\linkage_tpl.html";i:1551065534;}*/ ?>
<!DOCTYPE HTML>
<html><head>
    
    <link type="text/css" rel="stylesheet" href="http://mch.new.com/static/index_admin/css/fenlei.css" />
    <link type="text/css" rel="stylesheet" href="http://mch.new.com/static/index_admin/css/skin.css" />

    <link type="text/css" rel="stylesheet" href="http://mch.new.com/static/index_admin/css/fenlei.css" />
    <link type="text/css" rel="stylesheet" href="http://mch.new.com/static/index_admin/css/skin.css" />
    <!--前置自定义js-->
    
    <script type="text/javascript" src="http://mch.new.com/static/common/js/jquery/jquery-1.9.1.min.js"></script>
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
                        <td class=dotted_bottom_gray height=40 width="11%" align=right><font color="#FF0000">*</font>所属分类：</td>
                        <td class=dotted_bottom_gray width="89%" align=left>
                            <select name="category_id_1" class="selectbox">
    <option value="">一级分类</option>
</select>
<select name="category_id_2" class="selectbox">
    <option value="">二级分类</option>
</select>
<select name="category_id_3" class="selectbox">
    <option value="">三级分类</option>
</select>
<!-- 隐藏区 -->
<div style="display:none">
    <select id="allCategoryListTemp">
        <?php if(is_array($allCategoryList) || $allCategoryList instanceof \think\Collection || $allCategoryList instanceof \think\Paginator): $i = 0; $__LIST__ = $allCategoryList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
            <option value="<?php echo htmlentities($vo['id']); ?>" level="<?php echo htmlentities($vo['level']); ?>" parent_id_1="<?php echo htmlentities($vo['parent_id_1']); ?>"
                    parent_id_2="<?php echo htmlentities($vo['parent_id_2']); ?>"><?php echo htmlentities($vo['name']); ?></option>
        <?php endforeach; endif; else: echo "" ;endif; ?>
    </select>
</div>
                        </td>
                    </tr>
                    <tr>
                        <td class=dotted_bottom_gray height=40 width="11%" align=right><font color="#FF0000">*</font>商品名称：</td>
                        <td class=dotted_bottom_gray width="40%" align=left>
                            <input style="width: 200px; " class="inputboxadmin" name="name" value="<?php echo htmlentities($info['name']); ?>">

                        </td>
                    </tr>
                    <tr>
                        <td class=dotted_bottom_gray height=40 width="11%" align=right><font color="#FF0000">*</font>标题文案：</td>
                        <td class=dotted_bottom_gray width="40%" align=left>
                            <input style="width: 200px; " class="inputboxadmin" name="headline" value="<?php echo htmlentities($info['headline']); ?>">
                        </td>
                    </tr>
                    <tr class="radioTypeOption first">
                        <td class=dotted_bottom_gray height=40 width="11%" align=right><font color="#FF0000">*</font>批量价格：</td>
                        <td class=dotted_bottom_gray width="89%" align=left>
                            <input style="width: 200px; " class="inputboxadmin" name="bulk_price" value="<?php echo htmlentities($info['bulk_price']); ?>">
                        </td>
                    </tr>
                    <tr class="radioTypeOption first">
                        <td class=dotted_bottom_gray height=40 width="11%" align=right><font color="#FF0000">*</font>样品价格：</td>
                        <td class=dotted_bottom_gray width="89%" align=left>
                            <input style="width: 200px; " class="inputboxadmin" name="sample_price" value="<?php echo htmlentities($info['sample_price']); ?>">
                        </td>
                    </tr>
                    <tr>
                        <td class=dotted_bottom_gray height=40 width="11%" align=right>商品规格：</td>
                        <td class=dotted_bottom_gray width="89%" align=left>
                            <input style="width: 200px;" class="inputboxadmin" name="specification" value="<?php echo htmlentities($info['specification']); ?>">
                        </td>
                    </tr>
                    <tr>
                        <td class=dotted_bottom_gray height=40 width="11%" align=right>起订数量：</td>
                        <td class=dotted_bottom_gray width="89%" align=left>
                            <input style="width: 200px;" class="inputboxadmin" name="minimum_order_quantity" value="<?php echo htmlentities($info['minimum_order_quantity']); ?>">
                        </td>
                    </tr>
                    <tr>
                        <td class=dotted_bottom_gray height=40 width="11%" align=right>起订递增数量：</td>
                        <td class=dotted_bottom_gray width="89%" align=left>
                            <input style="width: 200px;" class="inputboxadmin" name="increase_quantity" value="<?php echo htmlentities($info['increase_quantity']); ?>">
                        </td>
                    </tr>
                    <tr>
                        <td class=dotted_bottom_gray height=40 width="11%" align=right>样品购买限额：</td>
                        <td class=dotted_bottom_gray width="89%" align=left>
                            <input style="width: 200px;" class="inputboxadmin" name="minimum_sample_quantity" value="<?php echo htmlentities($info['minimum_sample_quantity']); ?>">
                        </td>
                    </tr>
                    <tr>
                        <td class=dotted_bottom_gray height=40 width="11%" align=right>采购单位：</td>
                        <td class=dotted_bottom_gray width="89%" align=left>
                            <select name="purchase_unit">
                                <option value="">请选择单位</option>
                                <?php if(is_array($unitList) || $unitList instanceof \think\Collection || $unitList instanceof \think\Paginator): $k = 0; $__LIST__ = $unitList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$unit): $mod = ($k % 2 );++$k;?>
                                <option value="<?php echo htmlentities($k); ?>"><?php echo htmlentities($unit); ?></option>
                                <?php endforeach; endif; else: echo "" ;endif; ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td class=dotted_bottom_gray height=40 width="11%" align=right>排序：</td>
                        <td class=dotted_bottom_gray width="89%" align=left>
                            <input style="width: 100px; " class="inputboxadmin" name="sort" value="<?php echo htmlentities($info['sort']); ?>">
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
                        <td class=dotted_bottom_gray height=40 width="11%" align=right>商品视频：</td>
                        <td class=dotted_bottom_gray width="89%" align=left>
                            <div class= width="89%" align=left>
                                <p class="f20 picture-tipc-box"><span class="friend-tipc">*注意</span>：必须上传视频(mp4、rmvb、avi、ts格式视频)</p>
                                <form>
                                    <div class="upload-picture-module f24">
                                        <div>
                                            <div class="picture-module">
                                                <input type="file" class="uploadImg uploadSingleVideo" name="">
                                                <?php if(empty($info['goods_video']) || (($info['goods_video'] instanceof \think\Collection || $info['goods_video'] instanceof \think\Paginator ) && $info['goods_video']->isEmpty())): ?>
                                                <video class="upload_img upload-video" src="" autoplay="autoplay"></video>
                                                <?php else: ?>
                                                <video class="upload_img upload-video" src="http://mch.new.com/uploads/<?php echo htmlentities($info['goods_video']); ?>" autoplay="autoplay"></video>
                                                <?php endif; ?>
                                                <input type="hidden" class="layer-thumbnail-picture img"  value="<?php echo htmlentities($info['goods_video']); ?>" name="goods_video"/>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class=dotted_bottom_gray height=40 width="11%" align=right>商品简介：</td>
                        <td class=dotted_bottom_gray width="89%" align=left>
                            <div class= width="89%" align=left>
                                <textarea class="project_description" name="intro" ><?php echo htmlentities($info['intro']); ?></textarea>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class=dotted_bottom_gray height=40 width="11%" align=right>商品参数：</td>
                        <td class=dotted_bottom_gray width="89%" align=left>
                            <script id="param" type="text/plain">

                            </script>
                        </td>
                    </tr>
                    <tr>
                        <td class=dotted_bottom_gray height=40 width="11%" align=right>主图：</td>
                        <td class=dotted_bottom_gray width="89%" align=left>
                            <script id="main_img" type="text/plain"></script>
                            <input id="mainImg" type="hidden" value="<?php echo htmlentities(formatImg($info['main_img'])); ?>">
                        </td>
                    </tr>
                    <tr>
                        <td class=dotted_bottom_gray height=40 width="11%" align=right>商品详情图：</td>
                        <td class=dotted_bottom_gray width="89%" align=left>
                        <script id="detail_img" type="text/plain"></script>
                        <input id="detailImg" type="hidden" value="<?php echo htmlentities(formatImg($info['detail_img'])); ?>">
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

<script type="text/javascript" src="http://mch.new.com/static/common/js/public.js"></script>
<script type="text/javascript" src="http://mch.new.com/static/common/js/common.js"></script>
<script type="text/javascript" src="http://mch.new.com/static/h-ui.lib/layer/2.4/layer.js"></script>
<script type="text/javascript" src="http://mch.new.com/static/common_admin/js/dialog.js"></script>
<script type="text/javascript" src="http://mch.new.com/static/common_admin/js/paging.js"></script>
<!--自定义js-->

    <script type="text/javascript" src="http://mch.new.com/static/h-ui.lib/Ueditor/ueditor.config.js"></script>
    <!-- 编辑器源码文件 -->
    <script type="text/javascript" src="http://mch.new.com/static/h-ui.lib/Ueditor/ueditor.all.js"></script>
    <script type="text/javascript" charset="utf-8" src="http://mch.new.com/static/h-ui.lib/Ueditor/lang/zh-cn/zh-cn.js"></script>
    <script src="https://cdn.bootcss.com/html5media/1.1.8/html5media.min.js"></script>
    <!-- 实例化编辑器 -->
    <script type="text/javascript">
        var ueParam = UE.getEditor('param', {
            toolbars: [
                [  'fullscreen', 'source', '|', 'undo', 'redo', '|',
                    'bold', 'italic', 'underline', 'fontborder', 'strikethrough', 'superscript', 'subscript', 'removeformat', 'formatmatch', 'autotypeset', 'blockquote', 'pasteplain', '|', 'forecolor', 'backcolor', 'insertorderedlist', 'insertunorderedlist', 'selectall', 'cleardoc', '|',
                    'rowspacingtop', 'rowspacingbottom', 'lineheight', '|',
                    'customstyle', 'paragraph', 'fontfamily', 'fontsize', '|',
                    'directionalityltr', 'directionalityrtl', 'indent', '|',
                    'justifyleft', 'justifycenter', 'justifyright', 'justifyjustify', '|', 'touppercase', 'tolowercase', '|',
                    'anchor', '|', 'emotion', 'scrawl',   'webapp', 'pagebreak', 'template', 'background', '|',
                    'horizontal', 'date', 'time', 'spechars', 'snapscreen', 'wordimage', '|',
                    'inserttable', 'deletetable', 'insertparagraphbeforetable', 'insertrow', 'deleterow', 'insertcol', 'deletecol', 'mergecells', 'mergeright', 'mergedown', 'splittocells', 'splittorows', 'splittocols', 'charts', '|',
                    'print', 'preview', 'searchreplace', 'drafts', 'help']
            ],
            autoHeightEnabled: true,
            autoFloatEnabled: true
        });
        var ueDetail = UE.getEditor('detail_img', {
            toolbars: [
                ['imagenone', 'imageleft', 'imageright', 'imagecenter', 'simpleupload', 'insertimage']
            ],
            autoHeightEnabled: true,
            autoFloatEnabled: true
        });
        var ueNotices = UE.getEditor('main_img', {
            toolbars: [
                ['imagenone', 'imageleft', 'imageright', 'imagecenter', 'simpleupload', 'insertimage']
            ],
            autoHeightEnabled: true,
            autoFloatEnabled: true
        });
    </script>
    <script type="text/javascript" src="http://mch.new.com/static/index_admin/js/categoryLinkage.js"></script>
    <script type="text/javascript">
        $(function(){
            var parameters = '<?php echo $info['parameters']; ?>';
            //商品参数
            if(parameters){
                ueParam.ready(function() {
                    ueParam.setContent(parameters);
                });
            }

            //商品主图
            ueNotices.ready(function() {
                ueNotices.setContent($('#mainImg').val());
            });
            //商品详情图
            ueDetail.ready(function() {
                ueDetail.setContent($('#detailImg').val());
            });

            //初始化所属分类
            var category_id_1 = '<?php echo htmlentities($info['category_id_1']); ?>';
            if(category_id_1){
                $('[name=category_id_1]').val(category_id_1);
                $('[name=category_id_1]').change();
            }
            var category_id_2 = '<?php echo htmlentities($info['category_id_2']); ?>';
            if(category_id_2){
                $('[name=category_id_2]').val(category_id_2);
                $('[name=category_id_2]').change();
            }

            var purchase_unit = '<?php echo htmlentities($info['purchase_unit']); ?>';
            if(purchase_unit){
                $('[name=purchase_unit]').val(purchase_unit);
            }
            // 选择单视频
            $('body').on('change','.uploadSingleVideo',function () {
                _this = $(this);
                uploadsSingleVideoFlag = false;
                var video = event.target.files[0];
                var obj=$(this).parent();
                // 判断是否视频
                if(!video){
                    return false;
                }
                // 判断视频格式
                var imgRegExp=/\.(?:mp4|rmvb|avi|ts)$/;
                if(!(video.type.indexOf('video')==0 && video.type && imgRegExp.test(video.name)) ){
                    // dialog.error('请上传：mp4、rmvb、avi、ts格式图片');
                    return false;
                }
                var reader = new FileReader();
                reader.readAsDataURL(video);
                reader.onload = function(e){
                    var postData = {fileBase64: e.target.result};
                    var videoUrl=e.target.result;
                    $(obj).find('video').attr('src',videoUrl);
                    //提交
                    var type = _this.data('type');
                    if(type == 'notupload'){
                        $(obj).find('.img').val(videoUrl);
                        return false;
                    }
                    $.post(controller+"uploadFileToTemp",postData,function(msg){
                        if(msg.status == 1){
                            uploadsSingleVideoFlag = true;
                            $(obj).find('.img').val(msg.info);
                        }else{
                            dialog.error(msg.info)
                        }
                    })

                }
            });
            //确定
            $('.button_save_black_4').on('click',function(){
                var postData = $('#form1').serializeObject();
                //缩略图
//                postData.thumb_img = $('img.thumb_img').attr('src');
                //主图
                postData.main_img = '';
                $.each($('#main_img').find('iframe').contents().find('.view img'),function(){
                    postData.main_img += $(this).attr('src') + ',';

                });
                //详情图
                postData.detail_img = '';
                $.each($('#detail_img').find('iframe').contents().find('.view img'),function () {
                    postData.detail_img += $(this).attr('src') + ',';
                });
                //简介
                postData.parameters = ueParam.getContent();

                var content='';
                if(!postData.name){
                    content='请填写商品名称';
                }else if(!postData.headline){
                    content='请填写商品标题文案';
                }
//                else if(!postData.bulk_price){
//                    content='请填写批量价格';
//                }else if(!isMoney(postData.bulk_price)){
//                    content='价格格式有误';
//                }else if(!postData.sample_price){
//                    content='请填写样品价格';
//                }else if(!isMoney(postData.sample_price)){
//                    content='样品价格格式有误';
//                }
                else if(!postData.specification){
                    content='请填写商品规格';
                }else if(!postData.main_img){
                    content='请上传主图';
                }else if(!postData.detail_img){
                    content='请上传商品详情图';
                }
                if(content){
                    dialog.error(content);
                    return false;
                }

                var _this = $(this);
                _this.addClass("nodisabled");//防止重复提交
                $.post(action,postData,function(data){
                    _this.removeClass("nodisabled");//防止重复提交
                    dialog.msg(data,'',function () {
                        location.href = controller + 'manage';
                    });
                });
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