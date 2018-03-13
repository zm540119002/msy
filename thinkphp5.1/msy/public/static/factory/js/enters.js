$(function(){
    tab_down('.apply-data-nav .switch-item','.apply-module','click');
    var factoryFullName,
        agentName,
        businessLicense,
        agentAuthorization,
        postData={};
    //填写基本资料
    $('body').on('click','.one-step',function(){
        factoryFullName=$('.factoryFullName').val();
        agentName=$('.agentName').val();
        var content='';
        if(!factoryFullName){
            content='请填写厂商全称';
        }else if(!agentName){
            content='请填写代办人姓名';
        }
        if(content){
            dialog.error(content);
        }else{
            $('.weui-flex-item:eq(0)').removeClass('current');
            $('.weui-flex-item:eq(1)').addClass('current');
            $('.apply-module:eq(0)').hide();
            $('.apply-module:eq(1)').show();
        }
    });

    // 上传图片资料
    $('body').on('change','.uploadImg',function(){
        
    });
    //验证是否上传图片
    $('body').on('click','.two-step',function(){
        factoryFullName=$('.factoryFullName').val();
        agentName=$('.agentName').val();
        businessLicense=$('.business-license').val();
        agentAuthorization=$('.agent-authorization').val();
        var content='';
        if(!factoryFullName){
            content='请填写厂商全称';
        }else if(!agentName){
            content='请填写代办人姓名';
        }else if(!businessLicense){
            content='请上传企业营业执照照片';
        }else if(!agentAuthorization){
            content='请上传代办人企业授权信照片';
        }
        postData={
            name:factoryFullName,
            agent:agentName,
            business_license:businessLicense,
            auth_letter:agentAuthorization
        };
        if(content){
            dialog.error(content);
            return false;
        }
        $.post("enters",postData,function(info){
            $('.weui-flex-item:eq(1)').removeClass('current');
            $('.weui-flex-item:eq(2)').addClass('current');
            $('.apply-module:eq(1)').hide();
            $('.apply-module:eq(2)').show();
        })
    });
    //确定通过入驻
    $('body').on('click','.three-step',function(){
        
    })

});
