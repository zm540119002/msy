<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title><?php echo (C("WEB_NAME")); ?></title>
    <meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=no">
    <link rel="stylesheet" href="/Public/css/common/public.css">
    <link rel="stylesheet" href="/Public/css/common/weui.css">
    <script type="text/javascript" src="/Public/js/common/jquery-1.11.3.min.js"></script>
    
    <link rel="stylesheet" type="text/css" href="/Public/css/home/mall.css">

</head>

<body>
<!-- 基础导航模块，子页面可覆盖 -->
<!--
<div class="header">
    <a class="back" href="javascript:history.go(-1)"></a>
    <?php echo (C("WEB_NAME")); ?>
    <a href="<?php echo U('Index/index');?>" class="set"></a>
</div>


<div class="top_space"></div>-->

<!-- 基础内容模块，子页面可覆盖 -->

    <!-- S= 轮播图 -->
    <div id="slider">
        <div class='swipe-wrap'>
            <div><img src="/Public/img/home/slide/banner.jpg" /></div>
            <div><img src="/Public/img/home/slide/diamond_watches.jpg" /></div>
            <div><img src="/Public/img/home/slide/red_wine.jpg" /></div>
        </div>
        <ul class="position">
            <li class="on"></li>
            <li></li>
            <li></li>
        </ul>
    </div>
    <nav class="f24">
        <ul class="common_top_nav">
            <li class="current">美妍商城</li>
            <li>美社预约</li>
        </ul>
    </nav>
    <div class="common_contents f24">
        <div class="common_tablist_wrap">
            <nav class="mall_function_menu f24">
                <a href="<?php echo U('Goods/studioSpeList');?>">
                    <span class="purchaser_register"></span>
                    商城促销
                </a>
                <a href="<?php echo U('Goods/goodsGroup');?>">
                    <span class="vip"></span>
                    微团购
                </a>
                <a href="<?php echo U('Goods/goodsIndex');?>">
                    <span class="project_class"></span>
                    商品分类
                </a>
                <a href="<?php echo U('Goods/goodsGroup');?>">
                    <span class="recharge"></span>
                    推客分享
                </a>
                <a href="<?php echo U('PersonalCenter/index');?>">
                    <span class="order_manage"></span>
                    个人中心
                </a>
            </nav>
            <!--代金券-->
            <section class="ms_cash_coupon">
                <div class="top_bar">
                    <span>代金券</span>
                    <a href="">领券中心></a>
                </div>
                <ul class="cash_coupon_list">
                    <li>
                        <span>XX</span>
                        <span>代金券</span>
                        <span class="amount_money f32"><price>50</price>元</span>
                        <a href="javascript:void(0);">领取></a>
                    </li>
                    <li>
                        <span>XX</span>
                        <span>代金券</span>
                        <span class="amount_money f32"><price>50</price>元</span>
                        <a href="javascript:void(0);">领取></a>
                    </li>
                    <li>
                        <span>XX</span>
                        <span>代金券</span>
                        <span class="amount_money f32"><price>50</price>元</span>
                        <a href="javascript:void(0);">领取></a>
                    </li>
                </ul>
            </section>

            <div class="promotion_mode_type f24">
                <div class="platform_mode">
                    <!--<h2>特色产品</h2>-->
                    <a href="<?php echo U('Goods/categoryGoods',array('categoryId'=> $gCategoryList[0]['id']));?>">
                        <?php if(!empty($gCategoryList[0]['img'])): ?><img src="/Uploads/<?php echo ($gCategoryList[0]['img']); ?>" alt="">
                            <?php else: ?>
                            <div class='category-left'>
                                <?php echo ($gCategoryList[0]['name']); ?>
                            </div><?php endif; ?>
                    </a>
                </div>
                <div class="platform_purchase">
                    <div class="platform_toker">
                        <!--<h2>基础产品</h2>-->
                        <a href="<?php echo U('Goods/categoryGoods',array('categoryId'=> $gCategoryList[1]['id']));?>">
                            <?php if(!empty($gCategoryList[1]['img'])): ?><img src="/Uploads/<?php echo ($gCategoryList[1]['img']); ?>" alt="">
                                <?php else: ?>
                                <div class='category-right-top'>
                                    <?php echo ($gCategoryList[1]['name']); ?>
                                </div><?php endif; ?>
                        </a>
                    </div>
                    <div class="platform_theme">
                        <div class="theme_purchase">
                            <!--<h2>身体产品</h2>-->
                            <a href="<?php echo U('Goods/categoryGoods',array('categoryId'=> $gCategoryList[2]['id']));?>">
                                <?php if(!empty($gCategoryList[2]['img'])): ?><img src="/Uploads/<?php echo ($gCategoryList[2]['img']); ?>" alt="">
                                    <?php else: ?>
                                    <div class='category-right-l'>
                                        <?php echo ($gCategoryList[2]['name']); ?>
                                    </div><?php endif; ?>
                            </a>
                        </div>
                        <div class="theme_purchase">
                            <!--<h2>居家产品</h2>-->
                            <a href="<?php echo U('Goods/categoryGoods',array('categoryId'=> $gCategoryList[3]['id']));?>">
                                <?php if(!empty($gCategoryList[3]['img'])): ?><img src="/Uploads/<?php echo ($gCategoryList[3]['img']); ?>" alt="">
                                    <?php else: ?>
                                    <div class='category-right-r'>
                                        <?php echo ($gCategoryList[3]['name']); ?>
                                    </div><?php endif; ?>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <input type="hidden" class="goods_position" value="index">
            <div class="goods_content " ></div>
        </div>

        <div class="common_tablist_wrap" style="display:none;">
            <nav class="mall_function_menu f24">
                <a href="">
                    <span class="project_class"></span>
                    工作室
                </a>
                <a href="">
                    <span class="purchaser_register"></span>
                    美容预约
                </a>
                <a href="<?php echo U('Project/projectGroup');?>">
                    <span class="vip"></span>
                    闺蜜行
                </a>
                <a href="<?php echo U('Project/projectIndex');?>">
                    <span class="recharge"></span>
                    美容项目
                </a>
                <a href="">
                    <span class="order_manage"></span>
                    我的预约
                </a>
            </nav>
            <!--代金券-->
            <section class="ms_cash_coupon">
                <div class="top_bar">
                    <span>代金券</span>
                    <a href="">领券中心></a>
                </div>
                <ul class="cash_coupon_list">
                    <li>
                        <span>XX</span>
                        <span>代金券</span>
                        <span class="amount_money f32"><price>50</price>元</span>
                        <a href="javascript:void(0);">领取></a>
                    </li>
                    <li>
                        <span>XX</span>
                        <span>代金券</span>
                        <span class="amount_money f32"><price>50</price>元</span>
                        <a href="javascript:void(0);">领取></a>
                    </li>
                    <li>
                        <span>XX</span>
                        <span>代金券</span>
                        <span class="amount_money f32"><price>50</price>元</span>
                        <a href="javascript:void(0);">领取></a>
                    </li>
                </ul>
            </section>
            <input type="hidden" class="project_position" value="index">
            <div class="project_content" ></div>
        </div>
    </div>
    <!-- 隐藏区 -->
    <div style="display:none">
        <div id="loading">
            <div class='loading'><img src='/Public/img/home/loading.gif' alt='loading'></div>
        </div>
    </div>

<!-- 返回顶部-->

    <div class="right_sidebar">
    <a href="<?php echo U('Index/index');?>" class="f24">首页</a>
    <a href="javascript:void(0);" class="f24 backTop">顶部</a>
</div>

<!-- 加载更多-->

<!-- 弹窗登录和注册等页面 -->

    <section id="userLoginForm" style="display:none;">
    <div class="f24 bomb_box">
        <div class="ucenter_logo">
            <img src="/Public/img/common/ucenter_logo.png" alt="">
            <p>已注册用户通行证登陆</p>
        </div>
        <ul class="loginNav">
            <li class="current">账号密码登录</li>
            <li>短信验证码登陆</li>
        </ul>
        <form class="loginTab" id="formLogin">
            <div class="userLogin login_wrap active">
                <div class="login_item">
                    <span class="username_txt">账号</span>
                    <input class="username user_name" type="text" placeholder="6-16位数字、字母或下划线" name="name">
                </div>
                <div class="login_item">
                    <span class="password_txt">密码</span>
                    <input class="username password" type="password" placeholder="6-16数字或字母" name="password">
                </div>
            </div>
            <div class="smsLogin login_wrap hide">
                <div class="login_item">
                    <span class="username_txt">手机号</span>
                    <input class="username user_phone" type="tel" placeholder="请输入手机号" name="mobile_phone">
                    <a href="javascript:void(0);" class="mesg_code">获取验证码</a>
                </div>
                <div class="login_item">
                    <span class="username_txt txt3">验证码</span>
                    <input type="text" class="tel_code username input_txt3" placeholder="请输入收到的验证码" name="captcha">
                </div>
            </div>
            <input type="hidden" name="captcha_type" value="login" />
        </form>
        <div class="error_tipc" ></div>
        <div class="friend_tipc">
            <a href="javascript:void(0);" class="register_dialog">注册</a>
            <a href="javascript:void(0);" class="forget_dialog">忘记美尚通行证密码?重置密码</a>
        </div>
    </div>
</section>
<script type="text/javascript">
    var userLoginForm=$('#userLoginForm').html();
    //登录触发
    var loginLayer = null;
    function loginDialog(func){
        isMore=false;
        loginLayer = layer.open({
            className:'loginLayer',
            content:userLoginForm,
            // shadeClose:false,
            btn:'登录',
            success:function(){
                window.scrollTo(0,0);
                tab_down('.loginNav li','.loginTab .login_wrap','click');
                $('.layui-m-layershade').on('touchmove',function(e){
                    event.preventDefault(); 
                });
               isRolling($('.loginLayer'));
            },
            end:function(){
                isMore=true;
            },
            yes:function(index){
                var $layer = $('.loginLayer').find('.active');
                var _index =$('.loginLayer').find('.login_wrap.active').index();
                var content='';
                //验证
                switch(_index){
                    case 0:
                        var userName=$layer.find('.user_name').val();
                        var password=$layer.find('.password').val();
                        if(!checkAccount(userName)){
                            content='请输入正确用户名';
                        }else if(!register.pswCheck(password)){
                            content = "请输入6-16数字或字母的密码";
                        }
                        break;
                    case 1:
                        var userPhone=$layer.find('.user_phone').val();
                        var verifiCode=$layer.find('.tel_code').val();
                        if(!register.phoneCheck(userPhone)){
                            content='请输入正确手机号';
                        }else if(!register.vfyCheck(verifiCode)){
                            content = "请输入正确的验证码";
                        }
                        break;
                }
                if(content){
                    errorTipc(content);
                    return false;
                }

                var url = '<?php echo U("/Home/User/login");?>';
                var postData = $('.loginLayer').find('#formLogin').serializeObject();
                $.ajax({
                    url:url,
                    type:'post',
                    data:postData,
                    error:function(xhr){},
                    success:function(data){
                        if(data.status==0){
                            errorTipc(data.info);
                            return false;
                        }else if(data.status==1){
                            if(func && $.isFunction(func)){
                                func();
                            }
                            isMore=true;
                            layer.close(index);
                        }
                    }
                });
            }
        });
    };
    
    $(function(){
        //注册
        $('body').on('click','.register_dialog',function(){
            platformNotesDialog();
            //registerDialog();
        });

        //忘记密码
        $('body').on('click','.forget_dialog',function(){
            forgetPasswordDialog();
        });
        
    });
</script>

    <section id="userRegisterForm" style="display:none;">
    <div class="f24 bomb_box">
        <div class="ucenter_logo">
            <img src="/Public/img/common/ucenter_logo.png" alt="">
            <p>新用户通行证注册</p>
        </div>
        <form class="registerTab" id="formRegister">
            <div class="smsForgetPasswd forgetPasswd_wrap hide">
    <div class="login_item">
        <span class="username_txt txt3">用户名：</span>
        <input class="username user_name input_txt3" type="text" placeholder="6-16位数字、字母或下划线" name="name">
    </div>
    <div class="forgetPasswd_item">
        <span class="username_txt new_password">密码：</span>
        <input type="password" class="password username n_pass" placeholder="6-16数字或字母" name="pass_word">
    </div>
    <div class="forgetPasswd_item">
        <span class="username_txt new_password">确认密码：</span>
        <input type="password" class="cofirm_password username" placeholder="请再次确定新密码" name="re_password">
    </div>
    <div class="forgetPasswd_item">
        <span class="username_txt txt3">手机号：</span>
        <input class="username user_phone input_txt3" type="tel" placeholder="请输入手机号" name="mobile_phone">
        <a href="javascript:void(0);" class="mesg_code">获取验证码</a>
    </div>
    <div class="forgetPasswd_item">
        <span class="username_txt txt3">验证码：</span>
        <input type="text" class="tel_code username input_txt3" placeholder="请输入收到的验证码" name="captcha">
    </div>
</div>

            <input type="hidden" name="captcha_type" value="register" />
        </form>
        <div class="error_tipc" ></div>
        <div class="guide_friendly_tipc">
            <p class="register_explain">说明：美尚通行证适用于集团旗下美尚云、美尚会、美创会等平台</p>
        </div>
        <div class="friend_tipc">
            <a href="javascript:void(0);" class="register_back">返回登录</a>
        </div>
    </div>
</section>
<script type="text/javascript">
    var userRegisterForm=$('#userRegisterForm').html();
    //注册触发
    var registerLayer = null;
    function registerDialog(func){
        registerLayer = layer.open({
            className:'registerLayer',
            content:userRegisterForm,
            btn:['确定'],
            success:function(){
                $('.registerLayer .mesg_code').text('获取验证码');
                $('input[name="notice"]').attr('checked',true);
            },
            yes:function(index){
                var $layer=$('.registerLayer').find('.registerTab');
                //验证
                var userName=$layer.find('.user_name').val();
                var password=$layer.find('.password').val();
                var newPassword=$layer.find('.cofirm_password').val();
                var userPhone=$layer.find('.user_phone').val();
                var verifiCode=$layer.find('.tel_code').val();
                var content='';
                if(!checkAccount(userName)){
                    content='请输入正确用户名';
                }else if(!register.pswCheck(password)){
                    content = "请输入正确的密码";
                }else if(password!=newPassword){
                    content = "两次密码输入不一致";
                }else if(!register.phoneCheck(userPhone)){
                    content = "请输入正确的手机号码";
                }else if(!register.vfyCheck(verifiCode)){
                    content = "请输入正确的验证码";
                }
                if(content){
                    errorTipc(content);
                    return false;
                }

                var url = '<?php echo U("/Home/User/register");?>';
                var postData = $('.registerLayer').find('#formRegister').serializeObject();
                $.ajax({
                    url:url,
                    type:'post',
                    data:postData,
                    error:function(xhr){},
                    success:function(data){
                        if(data.status==0){
                            errorTipc(data.info);
                            return false;
                        }else if(data.status==1){
                            if(func && $.isFunction(func)){
                                func();
                            }
                            layer.close(index);
                        }
                    }
                });
            }
        });
    }
    $(function(){
        //返回登录
        $('body').on('click','.register_back',function(){
            loginDialog();
        })
    })
</script>

    <section id="userForgetPasswdForm" style="display:none;">
    <div class="f24 bomb_box">
        <div class="ucenter_logo">
            <img src="/Public/img/common/ucenter_logo.png" alt="">
            <p>重置通行证登陆密码</p>
        </div>
        <form class="registerTab" id="formReset">
            <div class="smsForgetPasswd forgetPasswd_wrap hide">
    <div class="login_item">
        <span class="username_txt txt3">用户名：</span>
        <input class="username user_name input_txt3" type="text" placeholder="6-16位数字、字母或下划线" name="name">
    </div>
    <div class="forgetPasswd_item">
        <span class="username_txt new_password">密码：</span>
        <input type="password" class="password username n_pass" placeholder="6-16数字或字母" name="pass_word">
    </div>
    <div class="forgetPasswd_item">
        <span class="username_txt new_password">确认密码：</span>
        <input type="password" class="cofirm_password username" placeholder="请再次确定新密码" name="re_password">
    </div>
    <div class="forgetPasswd_item">
        <span class="username_txt txt3">手机号：</span>
        <input class="username user_phone input_txt3" type="tel" placeholder="请输入手机号" name="mobile_phone">
        <a href="javascript:void(0);" class="mesg_code">获取验证码</a>
    </div>
    <div class="forgetPasswd_item">
        <span class="username_txt txt3">验证码：</span>
        <input type="text" class="tel_code username input_txt3" placeholder="请输入收到的验证码" name="captcha">
    </div>
</div>

            <input type="hidden" name="captcha_type" value="reset" />
        </form>
        <div class="error_tipc" ></div>
        <div class="friend_tipc">
            <a href="javascript:void(0);" class="forgetPasswd_back">返回登录</a>
        </div>
    </div>
</section>
<script type="text/javascript">
    var userForgetPasswdForm=$('#userForgetPasswdForm').html();
    //注册触发
    var forgetPasswdLayer = null;
    function forgetPasswordDialog(func){
        forgetPasswdLayer = layer.open({
            className:'forgetPasswdLayer',
            content:userForgetPasswdForm,
            btn:['确定'],
            success:function(){
                $('.forgetPasswdLayer .mesg_code').text('获取验证码');
            },
            yes:function(index){
                var $layer=$('.forgetPasswdLayer').find('.forgetPasswd_wrap');
                //验证
                var userName=$layer.find('.user_name').val();
                var password=$layer.find('.password').val();
                var newPassword=$layer.find('.cofirm_password').val();
                var userPhone=$layer.find('.user_phone').val();
                var verifiCode=$layer.find('.tel_code').val();
                var content='';
                if(!checkAccount(userName)){
                    content='请输入正确用户名';
                }else if(!register.pswCheck(password)){
                    content = "请输入正确的密码";
                }else if(password!=newPassword){
                    content = "两次密码输入不一致";
                }else if(!register.phoneCheck(userPhone)){
                    content = "请输入正确的手机号码";
                }else if(!register.vfyCheck(verifiCode)){
                    content = "请输入正确的验证码";
                }
                if(content){
                    errorTipc(content);
                    return false;
                }

                var url = '<?php echo U("/Home/User/forget_password");?>';
                var postData = $('.forgetPasswdLayer').find('#formReset').serializeObject();
                $.ajax({
                    url:url,
                    type:'post',
                    data:postData,
                    error:function(xhr){},
                    success:function(data){
                        if(data.status==0){
                            errorTipc(data.info);
                            return false;
                        }else if(data.status==1){
                            if(func && $.isFunction(func)){
                                func();
                            }
                            layer.close(index);
                        }
                    }
                });
            }
        });
    }
    $(function(){
        //返回登录
        $('body').on('click','.forgetPasswd_back',function(){
            loginDialog();
        })
    })
</script>

    <section id="platformNotes" style="display:none;">
    <div class="platformNoteText">
        <p>111111111111111111</p>
        <p>222222222222222222</p>
        <p>333333333333333333</p>
        <p>444444444444444444</p>
        <div class="agreem_marquee">
            <input type="checkbox" class="agreement_consent" id="agreementConsent" >
            <label for="agreementConsent">我已阅读并同意该协议</label>
        </div>
    </div>
</section>
<script type="text/javascript">
    var platformNotes=$('#platformNotes').html();
    var notesDialogLayer=null;
    function platformNotesDialog(){
        notesDialogLayer=layer.open({
            title:['《美尚平台使用须知》','border-bottom:1px solid #d9d9d9;'],
            className:'notesLayer',
            content:platformNotes,
            btn:['下一步'],
            success:function(){},
            yes:function(index){
                if($('.agreement_consent').is(':checked')){
                    registerDialog();
                    layer.close(index);
                }
            }
        });
    }
</script>


<!-- 基础页脚模块，子页面可覆盖 -->

    <footer class="f24">
        <span class="icp_icon">Copyright 2017 维雅生物科技 粤ICP备14021421号</span>
    </footer>


<!--js基本插件-->
<script type="text/javascript" src="/Public/js/common/layer.mobile/layer.js"></script>
<script type="text/javascript" src="/Public/js/common/swipe.js"></script>
<script type="text/javascript" src="/Public/js/common/public.js"></script>
<script type="text/javascript" src="/Public/js/common/common.js"></script>
<script type="text/javascript" src="/Public/js/common/dialog.js"></script>
<script type="text/javascript" src="/Public/js/common/footerCart.js"></script>
<script type="text/javascript">
    var MODULE = '/index.php/Mall';
    var ACTION = '/index.php/Mall/Index/index';
    var CONTROLLER = '/index.php/Mall/Index';
    var APP = '/index.php';
    var PUBLIC = '/Public';
    var ROOT = '';
    var UPLOAD = ROOT+'/Uploads/';
</script>






    <script type="text/javascript">
        //加载商品列表
        function getPage(url,obj,currentPage) {
            var postData = {};
            postData.p = currentPage ?  currentPage : 1;
            postData.pageSize = 4;
            if(obj){
                postData.categoryId =$(obj).data('categoryid');
                postData.buyType = $(obj).data('buytype');
            }
            if(url == "/index.php/Mall/Project/projectList"){
                var content_obj = $('.project_content');
                postData.position=$('.project_position').val();
            }
            if(url == "/index.php/Mall/Goods/goodsList"){
                var content_obj = $('.goods_content');
                postData.position=$('.goods_position').val();
            }

            $.ajax({
                url: url,
                data: postData,
                type: 'get',
                beforeSend: function(){
                    $(obj).html($('#loading').html());
                },
                success: function(data){
                    if(data.status == 0){
                        dialog.error(data.info);
                    }else{
                        if(obj){
                            $(obj).prev().find('li:last').after(data);
                            var page = $(obj).data('page')+1;
                            $(obj).data('page',page);
                        }else{
                            $(content_obj).html(data);
                        }

                    }
                },
                complete: function(){
                    $(obj).html('查看更多<i></i>')
                }
            });
        }

        $(function(){
            //初始化商品
            var goods_url = MODULE+'/Goods/goodsList';
            getPage(goods_url);

            //初始化项目
            var project_url = MODULE+'/Project/projectList';
            getPage(project_url);
            //查看更多
            $('body').on('click','.view_more',function () {
                var _this = $(this);
                var currentPage = _this.data('page');
                if(_this.data('type')=='project'){
                    getPage(project_url,_this,currentPage)
                }
                if(_this.data('type')=='goods'){
                    getPage(goods_url,_this,currentPage)
                }

            });

            //滑动轮播
            var textWidth=$('.beauty_title').width()+120;
            $('.beauty_title').css({width:textWidth/2+'px'});
             //滑动轮播
            var elem = document.getElementById('slider');
                swipe(elem);
            tab_down('.common_top_nav li','.common_contents .common_tablist_wrap','click');

        });
    </script>


</body></html>