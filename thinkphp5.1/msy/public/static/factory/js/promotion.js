/**
 * Created by Administrator on 2018/3/26.
 */
$(function(){
    var addsalesgoods=$('#addsalesgoods').html();
    //链接商品
    $('body').on('click','.linked-goods',function(){
        var pageii = layer.open({
            title:['选择促销商品','border-bottom:1px solid #d9d9d9;'],
            className:'addsalesgoodsLayer',
            type: 1,
            content:addsalesgoods,
            anim: 'up',
            style: 'position:fixed; left:0; top:0; width:100%; height:100%; border: none; -webkit-animation-duration: .5s; animation-duration: .5s;',
            success:function(){
                var winHeight=$(window).height();
                $('.signIn-wrapper').css('height',winHeight-120+'px');
                    //加载第一页
                    getPage();
            },
            yes:function(index){
                var promotionalId='';
                $.each($('.addsalesgoodsLayer .promotional-goods-list li'),function(){
                    var _this=$(this);
                    promotionalId+=_this.data('promotional-id')+',';
                })
                $('.linked-goods-id').val(promotionalId);
                console.log(promotionalId);
                layer.close(index);
            },
            btn:['确定','取消']
        });

    });
    //添加促销商品
    $('body').on('click','.all-goods-list a.goods',function(){
        var _this=$(this);
        var goodsId=_this.data('id');
        var goodsName=_this.find('.goods-name').text();
        var goodsImgSrc=_this.find('img').attr('src');
        var selectedLen=$('.addsalesgoodsLayer .promotional-goods-list li').length;
        // alert(selectedLen);
        var html='';

        if(_this.hasClass('current')){
            dialog.error('已添加');
            return false;
        }else{
            _this.addClass('current').siblings().removeClass('current');
            html+='<li data-promotional-id="'+goodsId+'"><img src="'+goodsImgSrc+'" alt=""/><span class="">'+goodsName+'</span><a href="javascript:void(0);" class="promotional-close-btn">X</a></li>';
            console.log(goodsImgSrc);
            if(!selectedLen){
                $('.promotional-goods-list').append(html);
            }else if(selectedLen==1){
                dialog.error('已添加');
                //$('.addsalesgoodsLayer .promotional-goods-list li').eq(0).before(html);
            }
        }

    });
    //移除促销商品
    $('body').on('click','.promotional-close-btn',function(){
        var _this=$(this);
        var promotionalId=_this.parent().data('promotional-id');
        $.each($('.all-goods-list a'),function(){
            var _This=$(this);
            if(_This.data('id')==promotionalId){
                _This.removeClass('current');
            }
        })
        _this.parent().remove();
    })
    $('body').on('click','.addSalesPromotion',function(){
        var postData=$('.addSalesPromotionForm').serializeObject();
        var content='';
        if(!postData.salesPromotionName){
            content="请填写促销活动名称";
        }else if(!postData.salesPromotionImg){
            content="请上传促销活动图片";
        }else if(!postData.specialPrice){
            content="请填写特价";
        }else if(!postData.startTime){
            content="请选择起始日期";
        }else if(!postData.endTime){
            content="请选择结束日期";
        }
        if(content){
            dialog.error(content);
            return false;
        }
    })

})
var currYear = (new Date()).getFullYear();
var opt={};
opt.datetime = {preset : 'datetime'};
opt.default = {
    theme: 'android-ics light', //皮肤样式
    display: 'modal', //显示方式
    mode: 'scroller', //日期选择模式
    dateFormat: 'yy-mm-dd',
    timeFormat: 'HH:ii',
    lang: 'zh',
    showNow: false,
    nowText: "今天",
    startYear: currYear - 100, //开始年份
    endYear: currYear + 100 //结束年份
};
$("#startTime").mobiscroll($.extend(opt['datetime'],opt['default']));
$("#endTime").mobiscroll($.extend(opt['datetime'],opt['default']));


//获取列表
function getPage(currentPage) {
    $("#list").html($('#loading').html());
    var url = module+'goods/getList';
    var postData = $('#form1').serializeObject();
    postData.page = currentPage ? currentPage : 1;
    postData.pageSize = 2;
    $.get(url, postData , function(data){
        console.log(data);
        $('.addsalesgoodsLayer #list').html(data);
    });
}

