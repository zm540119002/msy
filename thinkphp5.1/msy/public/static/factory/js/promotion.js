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
                var selectedGood={};
                var selectedGoods =[];
                var emptySpecialFlag  = 0;//未填价格标识位
                var errorSpecialFlag = 0; // 价格格式错误标识位
                var selectedLen=$('.addsalesgoodsLayer .promotional-goods-list li').length;
                if(!selectedLen){
                    errorTipc('请添加商品！');
                    return false;
                }
                var goodsName ='';
                $('.addsalesgoodsLayer .promotional-goods-list li').each(function () {
                    goodsName = $(this).find('.goods-name').text();
                    var goodsId = $(this).data('goods-id');
                    var special = $(this).find('.special').val();
                    if(!special){
                        emptySpecialFlag = 1;
                        return false;
                    }
                    if(!isMoney(special)) {
                        errorSpecialFlag = 1;
                        return false;
                    }
                    selectedGood={
                        goodsId:goodsId,
                        special:special,
                    }
                    selectedGoods.push(selectedGood);
                });
                if(emptySpecialFlag){
                    errorTipc(goodsName+'商品请填写特价');
                    return false;
                }
                if(errorSpecialFlag){
                    errorTipc(goodsName+'商品价格格式有误');
                    return false;
                }
                $('.linked-goods-id').val(JSON.stringify(selectedGoods));
                layer.close(index);
            },
            btn:['确定','取消']
        });

    });
    //添加促销商品
    $('body').on('click','.all-goods-list a.goods',function(){
        var selectedGoodsIds = [];
        $('.addsalesgoodsLayer .promotional-goods-list li').each(function(){
            var selectedGoodsId = $(this).data('goods-id');
            selectedGoodsIds.push(selectedGoodsId);
        });
        var _this=$(this);
        var goodsId=_this.data('id');
        if($.inArray(goodsId, selectedGoodsIds) !== -1){
            dialog.error('此商品已选择！');
            return false;
        }
        var goodsName=_this.find('.goods-name').text();
        var goodsImgSrc=_this.find('img').attr('src');
        var selectedLen=$('.addsalesgoodsLayer .promotional-goods-list li').length;
        var html='';
            html+='<li data-goods-id="'+goodsId+'"><img src="'+goodsImgSrc+'" alt=""/><span class="goods-name">'+goodsName+'</span><a href="javascript:void(0);" class="promotional-close-btn">X</a>' +
                '<span>特价</span><input type="text" class="special"></li>';
            if(!selectedLen){
                $('.promotional-goods-list').append(html);
            }else{
                $('.promotional-goods-list li:last').after(html);
            }
    });

    //搜索
    $('body').on('click','.addsalesgoodsLayer .search',function(){
        getPage();
    });
    //移除促销商品
    $('body').on('click','.promotional-close-btn',function(){
        var _this=$(this);
        var promotionalId=_this.parent().data('goods-id');
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
        postData.goods = $('.linked-goods-id').val();
        var content='';
        if(!postData.name){
            content="请填写促销活动名称";
        }else if(!postData.first_img) {
            content = "请上传一级页面广告图";
        }else if(!postData.first_img){
                content="请上传二级页面首焦图";
        }else if(!postData.goods){
            content="请链接商品";
        }else if(!postData.start_time){
            content="请选择起始日期";
        }else if(!postData.end_time){
            content="请选择结束日期";
        }
        if(content){
            dialog.error(content);
            return false;
        }
        $.ajax({
            url: controller + 'edit',
            data: postData,
            type: 'post',
            beforeSend: function(){

            },
            success: function(msg){
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
         $("#endTime").mobiscroll().datetime({
             theme: 'android-ics light',  
             display: 'modal', //显示方式
             mode: 'scroller', //操作方式 
             dateFormat: 'yy-mm-dd',
             timeFormat: 'HH:ii',
             lang: 'zh',  
             minDate:new Date(validity[0], validity[1] - 1,hm[0],hm1[0],hm1[1]),
         });
      }
   } 
};
$("#startTime").mobiscroll($.extend(opt['datetime'],opt['default']));

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

