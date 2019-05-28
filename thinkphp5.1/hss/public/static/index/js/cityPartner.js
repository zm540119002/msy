$(function(){
    tab_down('.apply-data-nav .switch-item','.apply-module','click');
    var area_address,
        applicantData={};
    //填写基本资料
    $('body').on('click','.search-city',function(){
        area_address =$('.area-address-name').getArea();
        if(!area_address){
            dialog.error('请选择城市');
            return false;
        }
        var cityArr=[];
		for (var key=0;key<cityList.length;key++) {
			cityArr.push([parseInt(cityList[key].province),parseInt(cityList[key].city)]);
		}
        var cityData=[];
        cityData.push(parseInt(area_address[0]),parseInt(area_address[1]));
        var provinces=arrayHasElement(cityArr,cityData);
        if(!provinces){
            layer.open({
                content:'所查询的城市可以申请城市合伙人<br/>声明：同一城市可能存在多位申请人,同等条件下按先申请先审核签约原则。',
                btn:['确定'],
                yes:function(index){
                    $('.weui-flex-item:eq(0)').removeClass('current');
                    $('.weui-flex-item:eq(1)').addClass('current');
                    $('.apply-module:eq(0)').hide();
                    $('.apply-module:eq(1)').show();
                    layer.close(index);
                }
            });
        }else{
            layer.open({
                content:'所查询的城市暂时没有空缺<br/>备注：城市合伙人可能已被签约，或者正处于保留状态，建议过段时间再查询。',
                btn:['确定'],
                yes:function(index){
                    layer.close(index);
                }
            });
           
        }
    });
     //填写基本资料
    $('body').on('click','.one-step',function(){
        applicantData=$('.applicant_form').serializeObject();
        var content=''; 
        if(!applicantData.company_name){
            content='请填写企业名称';
        }else if(!applicantData.applicant){
            content='请填写申请人姓名';
        }else if(!register.phoneCheck(applicantData.mobile)){
            content='请填写手机号码';
        }
        if(content){
            dialog.error(content); 
        }else{
            $('.weui-flex-item:eq(0),.weui-flex-item:eq(1)').removeClass('current');
            $('.weui-flex-item:eq(2)').addClass('current');
            $('.apply-module:eq(1)').hide();
            $('.apply-module:eq(2)').show();
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
        applicantData.pay_code = $('.pay_code').val();
        _this = $(this);
        if(!applicantData.pay_code){
            dialog.error('请选择结算方式');
        }else{
            submitApplicant(_this,applicantData);
        }
        
    });
});

var arrayHasElement = function(array, element) {  
    // 判断二维数组array中是否存在一维数组element
    for (var el of array) {
        if (el.length === element.length) {
        for (var index in el) {
            if (el[index] !== element[index]) {
            break;
        }
        // 判断二维数组array中是否存在一维数组element
            if (index == (el.length - 1)) {   
                return true;
            }
        }
        }
    }
    return false;
}
// 提交申请
function submitApplicant(_this,postData){
    var url = module + 'CityPartner/submitApplicant';
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