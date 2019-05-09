$(function(){

    alert(3333);
    tab_down('.apply-data-nav .switch-item','.apply-module','click');
    var area_address,
        applicantData={};
    //填写基本资料
    $('body').on('click','.one-step',function(){
        area_address =$('.area-address-name').getArea();
        applicantData=$('.applicant_form').serializeObject();
        var content='';
        if(!applicantData.name){
            content='请填写店家名称';
        }else if(!applicantData.applicant){
            content='请填写申请人姓名';
        }else if(!register.phoneCheck(applicantData.mobile)){
            content='请填写手机号码';
        }else if(!area_address){
            content='请选择地区';
        }else if(!applicantData.detail_address){
            content='请填写详细地址';
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
    
    //确定通过入驻
    $('body').on('click','.three-step',function(){
    });
    // 弹出支付方式
    $('body').on('click','.recharge_money',function(){
        var settlementMethod=$('.settlementMethod').html();
        layer.open({
            type: 1
            ,anim: 'up'
            ,style: 'position:fixed; bottom:0; left:0; width: 100%; height: 50%; padding:10px 0; border:none;',
            className:'settlementMethod bankTransferLayer',
            content: settlementMethod
        });
    });
    // 选择充值结算方式
    $('body').on('click','.settlementMethod .pay_nav li',function(){
        $(this).addClass('current').siblings().removeClass('current');
        var pay_code = $(this).data('paycode');
        $(this).find('input[type="checkbox"]').prop('checked',true);
        $('.pay_code').val(pay_code);
    });

    // 结算
    $('body').on('click','.settlement_btn',function () {
        applicantData.province = area_address[0];
        applicantData.city = area_address[1];
        applicantData.area = area_address[2];
        applicantData.pay_code = $('.pay_code').val();
        _this = $(this);
        if(!applicantData.pay_code){
            dialog.error('请选择结算方式');
        }else{
            submitApplicant(_this,applicantData);
        }
        
    });
});
// 提交申请
function submitApplicant(_this,postData){
    var url = module + 'Franchise/applyFranchise';
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
                location.href = data.url;

            }else{
                dialog.success(data.info);
                //dialog.error('结算提交失败!');
            }
        }
    });
}
