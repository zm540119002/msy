<?php /*a:2:{s:73:"/home/www/web/thinkphp5.1/mch/application/index_admin/view/user/info.html";i:1551064593;s:27:"template/admin_pc/base.html";i:1551077175;}*/ ?>
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
                    <li><a href="<?php echo url('User/info'); ?>"  class="current"><span>个人信息</span></a></li>
                </ul>
            </div>
        </div>
        <div style="margin-top:15px;">
            <form id="form1">
                <table id=table18 class="add_new_merchandise" width="100%">
                    <tbody>
                        <tr class="tr-margin-item">
                            <td class=dotted_bottom_gray width="12%" align="right">名称：</td>
                            <td class=dotted_bottom_gray width="88%" align=left>
                                <span><?php echo htmlentities((isset($info['name']) && ($info['name'] !== '')?$info['name']:'')); ?></span>
                                <input type="hidden" name="id" value="<?php echo htmlentities((isset($info['id']) && ($info['id'] !== '')?$info['id']:0)); ?>">
                            </td>
                        </tr>
                        <tr class="tr-margin-item">
                            <td class=dotted_bottom_gray width="12%" align="right">昵称：</td>
                            <td class=dotted_bottom_gray width="88%" align=left>
                                <span><?php echo htmlentities((isset($info['nickname']) && ($info['nickname'] !== '')?$info['nickname']:'')); ?></span>
                            </td>
                        </tr>
                        <tr class="tr-margin-item">
                            <td class=dotted_bottom_gray width="12%" align="right">性别：</td>
                            <td class=dotted_bottom_gray width="88%" align=left>
                                <span><?php echo htmlentities((isset($info['sex']) && ($info['sex'] !== '')?$info['sex']:'')); ?></span>
                            </td>
                        </tr>
                        <tr class="tr-margin-item">
                            <td class=dotted_bottom_gray width="12%" align="right">手机号码：</td>
                            <td class=dotted_bottom_gray width="88%" align=left>
                                <span><?php echo htmlentities((isset($info['mobile_phone']) && ($info['mobile_phone'] !== '')?$info['mobile_phone']:'')); ?></span>
                            </td>
                        </tr>
                        <tr class="tr-margin-item">
                            <td class=dotted_bottom_gray width="12%" align="right">注册时间：</td>
                            <td class=dotted_bottom_gray width="88%" align=left>
                                <span><?php echo htmlentities((date('Y-m-d H:i',!is_numeric($info['create_time'])? strtotime($info['create_time']) : $info['create_time']) ?: '')); ?></span>
                            </td>
                        </tr>
                        <tr class="tr-margin-item">
                            <td class=dotted_bottom_gray width="12%" align="right">最后登录时间：</td>
                            <td class=dotted_bottom_gray width="88%" align=left>
                                <span><?php echo htmlentities((date('Y-m-d H:i',!is_numeric($info['last_login_time'])? strtotime($info['last_login_time']) : $info['last_login_time']) ?: '')); ?></span>
                            </td>
                        </tr>
                        <tr class="tr-margin-item">
                            <td class=dotted_bottom_gray width="12%" align="right">备注：</td>
                            <td class=dotted_bottom_gray width="88%" align=left>
                                <span><?php echo htmlentities((isset($info['remark']) && ($info['remark'] !== '')?$info['remark']:'')); ?></span>
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