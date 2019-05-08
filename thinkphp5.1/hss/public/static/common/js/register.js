$(function(){
    tab_down('.apply-data-nav .switch-item','.apply-module','click');
    var name,
        applicant,
        mobile,
        area_address,
        detail_address,
        applicantData={};
    //填写基本资料
    $('body').on('click','.one-step',function(){
        // name=$('.name').val();
        // applicant=$('.applicant').val();
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
    //验证与 提交申请
    $('body').on('click','.two-step',function(){
        name=trim($('.name').val(),'g');
        applicant=trim($('.applicant').val(),'g');
        id = $('.id').val();
        var content='';
       var content='';
        if(!postData.name){
            content='请填写店家名称';
        }else if(!postData.applicant){
            content='请填写申请人姓名';
        }else if(!register.phoneCheck(postData.mobile)){
            content='请填写手机号码';
        }else if(!area_address){
            content='请选择地区';
        }else if(!postData.detail_address){
            content='请填写详细地址';
        }
        applicantData={
            id : id,
            name:name,
            applicant:applicant,
            mobile:mobile,
            area_address:area_address,
            detail_address:detail_address
        };
        if(content){
            dialog.error(content);
            return false;
        }
        var _this = $(this);
        _this.addClass("nodisabled");
        console.log(postData);
        $.post(controller + 'register',postData,function(msg){
            _this.removeClass("nodisabled");
            if(msg.status == 0){
                dialog.error(msg.info);
            }
            if(msg.status == 1){
                $('.weui-flex-item:eq(1)').removeClass('current');
                $('.weui-flex-item:eq(2)').addClass('current');
                $('.apply-module:eq(1)').hide();
                $('.apply-module:eq(2)').show();
                $('.weui-flex-item:eq(0)').addClass('disabled');
                $('.weui-flex-item:eq(1)').addClass('disabled');
                dialog.success(msg.info,module+'Store/index');
            }
        });
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
    // 选择充值支付方式
    $('body').on('click','.settlementMethod .pay_nav li',function(){
        $(this).addClass('current').siblings().removeClass('current');
        var pay_code = $(this).data('paycode');
        $(this).find('input[type="checkbox"]').prop('checked',true);
        $('.pay_code').val(pay_code);
    });

    // 提交订单带地址
    $('body').on('click','.settlement_btn',function () {

        var postData = {};
        postData.applicantData=applicantData; 
        postData.order_id = $('.order_id').val();
        postData.order_sn = $('.order_sn').val();
        postData.pay_code = $('.pay_code').val();
        _this = $(this);
        console.log(postData);
        submitApplicant(_this,postData);
    });
});
// 其它支付方式提交订单
function submitApplicant(_this,postData){
    var url = module + 'Order/confirmOrder';
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
