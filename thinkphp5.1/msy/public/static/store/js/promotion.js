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
                promotionalId+=$('.addsalesgoodsLayer .promotional-goods-list li').data('promotional-id');
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
            html+='<li data-promotional-id="'+goodsId+'"><img src="'+goodsImgSrc+'" alt=""/><span class="">'+goodsName+'</span><a href="javascript:void(0);" class="promotional-close-btn">X</a></li>';
            console.log(goodsImgSrc);
            if(!selectedLen){
                $('.promotional-goods-list').append(html);
            }else if(selectedLen==1){
                dialog.error('已添加');
            }
        // }

    });

    //搜索
    $('body').on('click','.addsalesgoodsLayer .search',function(){
        getPage();
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
    });
    $('body').on('click','.addSalesPromotion',function(){
        var postData=$('.addSalesPromotionForm').serializeObject();
        postData.start_time =  new Date(postData.start_time).getTime()/1000;
        postData.end_time = new Date(postData.end_time).getTime()/1000;
        postData.goods_id = $('.linked-goods-id').val();
        var content='';
        if(!postData.name){
            content="请填写促销活动名称";
        }else if(!postData.img){
            content="请上传促销活动图片";
        }else if(!postData.goods_id){
            content="请链接商品";
        }else if(!postData.promotion_price){
            content="请填写特价";
        }else if(!isMoney(postData.promotion_price)){
            content="价格格式有误";
        }else if(!postData.start_time){
            content="请选择起始日期";
        }else if(!postData.end_time){
            content="请选择结束日期";
        }
        if(content){
            dialog.error(content);
            return false;
        }
        var _this = $(this);
        _this.addClass("nodisabled");
        $.ajax({
            url: controller + 'edit',
            data: postData,
            type: 'post',
            beforeSend: function(){
                //$('.loading').show();
            },
            success: function(msg){
                _this.removeClass("nodisabled");
                if(msg.status == 0){
                    dialog.error(msg.info);
                    return false;
                }
                if(msg.status == 1){
                    dialog.success(msg.info,controller + 'manage');
                }
            },
            complete:function(){

            },
            error:function (xhr) {
                dialog.error('AJAX错误'+xhr);
            },
        });
    })

})
var currYear = (new Date()).getFullYear();
var opt={};
opt.date = {preset : 'date'};
opt.datetime = {preset : 'datetime'};
opt.time = {preset : 'time'};
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
    endYear: currYear + 100, //结束年份
    onSelect: function (valueText, inst) {
    //    console.log(inst);
      var id = $(this)[0].id;
      var validity = valueText.split("-");
      var hm=validity[2].split(' ');
      var hm1=hm[1].split(':');
      if (id === "startTime") {
          console.log(opt.default.maxDate);
         if (opt.default.maxDate) {
            opt.default.maxDate = null;
         }
         opt.default.minDate = new Date(validity[0], +validity[1] - 1, +validity[2] + 1);
         opt.default.minDate = new Date(validity[0], validity[1] - 1,hm[0],hm1[0]);
         console.log(opt.default.minDate);
         //$("#endTime").mobiscroll($.extend(opt['datetime'], opt['default']));
         $("#endTime").mobiscroll().datetime({
             theme: 'android-ics light',  
             display: 'modal', //显示方式
             mode: 'scroller', //操作方式 
             dateFormat: 'yy-mm-dd',
             timeFormat: 'HH:ii',
             lang: 'zh',  
             minDate:new Date(validity[0], validity[1] - 1,hm[0],hm1[0],hm1[1]),
            //stepMinute:1
         });
      }
   } 
};
$("#startTime").mobiscroll($.extend(opt['datetime'],opt['default']));
//$("#endTime").mobiscroll($.extend(opt['datetime'],opt['default']));

//获取商品列表
function getPage(currentPage) {
    $("#list").html($('#loading').html());
    var url = module+'goods/getList';
    var postData = $('.addsalesgoodsLayer #form1').serializeObject();
    postData.pageType = 'promotion';
    postData.page = currentPage ? currentPage : 1;
    postData.pageSize = 4;
    $.get(url, postData , function(data){
        $('.addsalesgoodsLayer #list').html(data);
    });
}

