
$(function(){
    $('body').on('click','.forgetPasswordBtn',function(){
        var $layer=$('.forgetPasswd_wrap');
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
            dialog.error(content);
            return false;
        }
        var url = action;
        var postData = $('#userForgetPasswdForm').serializeObject();
        $.post(url,postData,function (data) {
            if(data.status==0){
                dialog.error(data.info);
                return false;
            }else if(data.status==1){
                location.href = data.info;
            }
        });
    });
});