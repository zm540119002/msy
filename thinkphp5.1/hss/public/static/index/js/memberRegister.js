var area_address,
    applicantData={};

$(function(){
    //填写基本资料
    $('body').on('click','.next-step',function(){
        var _this = $(this);
        var data=$('.applicant_form').serializeObject();
        applicantData.applicant=data.applicant;
        applicantData.mobile=data.mobile;
        var content='';
        if(!applicantData.applicant){
            content='请填写你的姓名';
        }
        if(content){
            dialog.error(content);
            return false;
        }else{
            $('.memberRegTpl').parents('.layui-m-layer').remove();
            submitMemberInfo(_this,applicantData);
        }
    });
});
// 提交申请
function submitMemberInfo(_this,postData){
    //var url = module + '';
    _this.addClass("nodisabled");//防止重复提交
    $.ajax({
        url: url,
        data: postData,
        type: 'post',
        beforeSend: function(){
            $('.loading').show();
        },
        error:function(){
            $('.loading').hide();
            dialog.error('AJAX错误');
        },
        success: function(data){
            _this.removeClass("nodisabled");//删除防止重复提交
            
            $('.loading').hide();
            if(data.status){
                location.href = data.data.url;
            }else{
                dialog.success(data.info);
            }
        }
    });
}
