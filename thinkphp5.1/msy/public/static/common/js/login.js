var timer;
var requestSign = true;
//获取验证码
$('body').on('click','.mesg_code',function(){
    if($(this).attr('disabled')){
        return false;
    }
    var _form = $(this).parents('form');
    var postData = {};
    postData.mobile_phone = _form.find('[name=mobile_phone]').val();
    var userName=_form.find('.user_name').val();
    var userPhone=_form.find('.user_phone').val();
    if(!requestSign){
        return false;
    }
    var time=60;
    var content='';
    if(!register.phoneCheck(userPhone)){
        content='请输入正确手机号码';
    }
    if(content){
        errorTipc(content);
        return false;
    }
    $('.tel_code').val('');
    clearInterval(timer);
    timer=setInterval(CountDown,1000);
    function CountDown(){
        _form.find('.mesg_code').attr('disabled',true);
        _form.find('.mesg_code').text('重新发送'+time+'s');
        if(time==0){
            _form.find('.mesg_code').text("获取验证码").removeAttr("disabled");
            _form.find('.tel_code').val('');
            clearInterval(timer);
        }
        time--;
    }
    var url = send_sms;
    $.post(url,postData,function(msg){
        requestSign = true;
        if(msg.status == 0){
            $('.phone').val('').removeAttr("disabled");
            _form.find('.mesg_code').val("获取验证码").removeAttr("disabled");
            _form.find('.tel_code').val('');
            clearInterval(timer);
            errorTipc(msg.info,3000);
            return false;
        }else if(msg.status == 1){
            errorTipc("验证码已发送至手机:"+ postData.mobile_phone +' ，请查看。',3000);
            return false;
        }
    });
});

var userLoginForm=$('#userLoginForm').html();
//弹窗登录触发
var loginLayer = null;
function loginDialog(func){
    loadTrigger=false;
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
            loadTrigger=true;
        },
        yes:function(index){
            var $layer = $('.loginLayer').find('.active');
            var _index = $('.loginLayer').find('.login_wrap.active').index();
            var content='';
            //验证
            switch(_index){
                case 0:
                    var userPhone=$layer.find('.user_phone').val();
                    var verifiCode=$layer.find('.tel_code').val();
                    if(!register.phoneCheck(userPhone)){
                        content='请输入正确手机号';
                    }else if(!register.vfyCheck(verifiCode)){
                        content = "请输入正确的验证码";
                    }
                    break;
                case 1:
                    var userName=$layer.find('.user_name').val();
                    var password=$layer.find('.password').val();
                    if(!checkAccount(userName)){
                        content='请输入正确手机号';
                    }else if(!register.pswCheck(password)){
                        content = "请输入6-16数字或字母的密码";
                    }
                    break;
            }
            if(content){
                errorTipc(content);
                return false;
            }
            var url = action;
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
                        loadTrigger=true;
                        layer.close(index);
                    }
                }
            });
        }
    });
}

var userForgetPasswdForm=$('#userForgetPasswdForm').html();
//忘记密码触发
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
            var content='';
            var userPhone=$('.forgetPasswdLayer .loginTab').find('.user_phone').val();
            var password=$('.forgetPasswdLayer .loginTab').find('.password').val();
            var verifiCode=$('.forgetPasswdLayer .loginTab').find('.tel_code').val();
            if(!register.phoneCheck(userPhone)){
                content='请输入正确手机号';
            }else if(!register.vfyCheck(verifiCode)){
                content = "请输入正确的验证码";
            }else if(!register.pswCheck(password)){
                content = "请输入6-16数字或字母的密码";
            }
            if(content){
                errorTipc(content);
                return false;
            }else{
                layer.close(index)
            }
            

        }
    });
}
var attentionForm=$('#attentionForm').html();
//使用须知
$('body').on('click','.use-attention',function(){
    var pageii = layer.open({
            title:['《美尚平台使用须知》','border-bottom:1px solid #d9d9d9;'],
            className:'addCcountLayer',
            type: 1,
            content: attentionForm,
            anim: 'up',
            style: 'position:fixed; left:0; top:0; width:100%; height:100%; border: none; -webkit-animation-duration: .5s; animation-duration: .5s;',
            success:function(){
            },
            btn:['确定']
        });
});
//显示隐藏密码
var onOff=true;
$('body').on('click','.view-password',function(){
    var _this=$(this);
    if(onOff){
        _this.addClass('active');
        $('.login_item .password').attr('type','text');
        onOff=false;
    }else{
         _this.removeClass('active');
        $('.login_item .password').attr('type','password');
        onOff=true;
    }
});
$(function(){
    //弹窗忘记密码
    $('body').on('click','.forget_dialog',function(){
        forgetPasswordDialog();
    });
    //登录
    tab_down('.loginNav li','.loginTab .login_wrap','click');
    $('body').on('click','.loginNav li',function(){
        var _this=$(this);
        if(_this.index()==0){
            $('.login_item').find('.mesg_code').show();
            $('.use-item').show();
            $('.forget_password').hide();
            $('.entry-button').text('注册').removeClass('loginBtn').addClass('registerBtn');
            $('.login_item .password').attr('placeholder','设置密码');
        }else{
            $('.login_item').find('.mesg_code').hide();
            $('.use-item').hide();
             $('.forget_password').show();
            $('.entry-button').text('登录').removeClass('registerBtn').addClass('loginBtn');
            $('.login_item .password').attr('placeholder','密码');
            $('.login_wrap').removeClass('active');
        }
    });
    $('body').on('click','.loginBtn,.registerBtn',function(){
        var $layer = $('.loginTab').find('.active');
        var _index = $('.loginTab').find('.login_wrap.active').index();
        var content='';
        //验证
        switch(_index){
            case 1:
                var verifiCode=$layer.find('.tel_code').val();
                var userPhone=$('.loginTab').find('.user_phone').val();
                var password=$('.loginTab').find('.password').val();
                if(!register.phoneCheck(userPhone)){
                    content='请输入正确手机号码';
                }else if(!register.vfyCheck(verifiCode)){
                    // content = "请输入正确的验证码";
                }else if(!register.pswCheck(password)){
                    // content = "请输入密码";
                }
                break;
            default:
                // $layer.prev('.login_item').find('.mesg_code').hide();
                var userPhone=$('.loginTab').find('.user_phone').val();
                var password=$('.loginTab').find('.password').val();
                // $('.login_item').find('.mesg_code').hide();
                if(!register.phoneCheck(userPhone)){
                    content='请输入正确手机号';
                }else if(!register.pswCheck(password)){
                    content = "请输入6-16数字或字母的密码";
                }
                break;
        }
        if(content){
            dialog.error(content);
            return false;
        }
        var url = action;
        var postData = $('#formLogin').serializeObject();
        postData.returnUrl = '';
        $.post(url,postData,function (data) {
            if(data.status==0){
                dialog.error(data.info);
                return false;
            }else if(data.status==1){
                location.href = data.info;
            }
        });
    });
    //弹窗重置密码
    $('body').on('click','.forgetPasswdLayer .forgetPasswordBtn',function(){
        var $layer=$('.forgetPasswdLayer').find('.forgetPasswd_wrap');
        //验证
        //var userName=$layer.find('.user_name').val();
        var password=$layer.find('.password').val();
        var newPassword=$layer.find('.cofirm_password').val();
        var userPhone=$layer.find('.user_phone').val();
        var verifiCode=$layer.find('.tel_code').val();
        var content='';
        if(!register.pswCheck(password)){
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
        var url = controller + 'forgetPassword';
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
                    layer.close($layer);
                }
            }
        });
    });
});
