(function($){
    var deviceWidth=document.documentElement.clientWidth;
    var html =document.getElementsByTagName('html')[0];
    html.style.fontSize=deviceWidth/6.4+'px';

    $.fn.moreText = function(options){
        var defaults = {
            maxLength:50,
            mainCell:".branddesc",
            openBtn:'显示全部>',
            closeBtn:'收起'
        };
        return this.each(function() {
            var _this = $(this);

            var opts = $.extend({},defaults,options);
            var maxLength = opts.maxLength;
            var TextBox = $(opts.mainCell,_this);
            var openBtn = opts.openBtn;
            var closeBtn = opts.closeBtn;

            var countText = TextBox.html();
            var newHtml = '';
            if(countText.length > maxLength){
                newHtml = countText.substring(0,maxLength)+'...<span class="more">'+openBtn+'</span>';
            }else{
                newHtml = countText;
            }
            TextBox.html(newHtml);
            TextBox.on("click",".more",function(){
                if($(this).text()==openBtn){
                    TextBox.html(countText+' <span class="more">'+closeBtn+'</span>');
                }else{
                    TextBox.html(newHtml);
                }
            });
        });
    };

    //星星评分等级(5个img布局，有点重复)
    $.fn.setStar=function(options){
        var defaults={
            getFractionValue:1,
            mainCell:".star_img img",
            star:'public/admin-img/common/mall/star.png',
            starRed:'public/admin-img/common/mall/starred.png'
        };
        if($.isNumeric(options)){
            defaults.getFractionValue=options;
        }
        return this.each(function(){
            //console.log($(this));
            var _this=$(this);
            
            var opts=$.extend({},defaults,options); 
            var starBox = $(opts.mainCell,_this);
            var getFractionValue=opts.getFractionValue;
            var star=opts.star;
            var starRed=opts.starRed;
            var starValue=parseInt(getFractionValue);

            starBox.each(function(index){
                	
				var prompt=['1分','2分','3分','4分','5分'];	//评价分数
				this.id=index;		//遍历img元素，设置单独的id
                //console.log(this.id);
				starBox.attr('src',star);//空心星
				// _this.find('#'+getFractionValue).attr('src',starRed);		//当前的图片为实星
				// _this.find('#'+getFractionValue).prevAll().attr('src',starRed);	//当前的前面星星为实星  prompt[getFractionValue]
                _this.find('#'+(starValue-1)).attr('src',starRed);		//当前的图片为实星
				_this.find('#'+(starValue-1)).prevAll().attr('src',starRed);	//当前的前面星星为实星  prompt[getFractionValue]
                $(this).parent().next('span').text(getFractionValue+'分');
                $(this).parent().next('span').attr('data-score',getFractionValue);  
			});
        });
    };
    $.fn.getStar=function(){
         return this.find("span").attr('data-score');
    };

    //星星评分（绝对定位布局）
     $.fn.classStar=function(options){
        var defaults={
            getFractionValue:1,
            mainCell:".real_star",
            star:'public/admin-img/common/sellerCompany/star.png',
            starRed:'public/admin-img/common/sellerCompany/starred.png'
        };
        if($.isNumeric(options)){
            defaults.getFractionValue=options;
        }
        return this.each(function(){
            //console.log($(this));
            var _this=$(this);
            
            var opts=$.extend({},defaults,options); 
            var starBox = $(opts.mainCell,_this);
            var getFractionValue=opts.getFractionValue;
            var star=opts.star;
            var starRed=opts.starRed;
            var starValue=parseInt(getFractionValue)*25;

            starBox.each(function(index){             	
				// this.id=index;
                $(this).css('width',starValue+'px');
                $(this).parent().next('span').text(getFractionValue+'分');
                $(this).parent().next('span').attr('data-score',getFractionValue);  
			});
        });
    };
    $.fn.getClassStar=function(){
         return this.find("span").attr('data-store');
    };

    //进度条
    $.fn.getProgressBar=function(options){
        var defaults={
            getProgressValue:1,
            mainCell:".real_star_progress"
        };
        if($.isNumeric(options)){
            defaults.getProgressValue=options;
        }
        return this.each(function(){
            // console.log($(this));
            var _this=$(this);
            
            var opts=$.extend({},defaults,options); 
            var progressBox = $(opts.mainCell,_this);
            var getProgressValue=opts.getProgressValue;
            var progressValue=parseInt(getProgressValue)*10;
            progressBox.each(function(index){
                // this.id=index;
                $(this).css('width',progressValue+'px');
			});
        });
    };
})(jQuery);

//限制input、textarea字数
var maximumWord =function(obj,max){
    var val=$(obj).val().length;
    var content='最多只能输入'+max+'个字';
    if(val>max){
        layer.open({
            content:content,
            time:2
        });
        $(obj).val($(obj).val().substring(0,max));
        return false;
    }
};

//选项卡切换
$.fn.tab = function(){
    $(this).addClass("current").siblings().removeClass("current");
};

//选项卡切换
function tab_down(tab_k, tab_con, tab_dz) {
    var $div_li = $(tab_k);
    var timeout;
    if (tab_dz == "click") {
        $div_li.click(function() {
            $(this).addClass("current").siblings().removeClass("current");
            var index = $div_li.index(this);
            $(tab_con).hide().eq(index).show().addClass('active').siblings().removeClass('active');
        })
    } else if (tab_dz == "mouseover") {
        $div_li.hover(function() {
            var ts = $(this);
            timeout = setTimeout(function() {
                ts.addClass("current").siblings().removeClass("current");
                var index = ts.index();
                // $(tab_con).eq(index).show().siblings().hide();
                $(tab_con).hide().eq(index).show();
            }, 200)
        }, function() {
            clearTimeout(timeout);
        })
    }
}

//加载图片
function checkShow(ele){
    var winH=$(window).height(),
        scrollH=$(window).scrollTop();
    ele.each(function(){
        var _This=$(this),top;
        top =_This.offset().top;
        if(_This.attr('data-isloaded')){
            return ;
        }
        if(top < scrollH + winH){
            setTimeout(function(){
                // owImg(_This);
                _This.attr('src',_This.attr('data-img'));
                _This.attr('data-isloaded',true);
            },300)
        }
    })
}

//全选
$('body').on('click','.checkall',function () {
    var _thisChecked = $(this).prop("checked");
    $.each($('.checkitem'),function () {
        $(this).prop('checked',_thisChecked);
    });
});

//反选
$('body').on('click','.checkitem',function () {
    var sign = true;
    //一票否决
    $.each($('.checkitem'),function () {
        if(!$(this).prop('checked')){
            sign = false;
        }
    });
    $('.checkall').prop('checked',sign);
});

//滑动轮播
function swipe(elemObj){
    window.mySwipe = Swipe(elemObj, {
        auto: 2500,
        callback: function(index,element){
        }
    });
}

//活动倒计时
function countDown(time,id){
    var day_elem = $(id).find('.day');
    var hour_elem = $(id).find('.hour');
    var minute_elem = $(id).find('.minute');
    var second_elem = $(id).find('.second');
    var end_time = new Date(time).getTime(),//月份是实际月份-1
        sys_second = (end_time-new Date().getTime())/1000;
    var timer = setInterval(function(){
        if (sys_second > 1) {
            sys_second -= 1;
            var day = Math.floor((sys_second / 3600) / 24);
            var hour = Math.floor((sys_second / 3600) % 24);
            var minute = Math.floor((sys_second / 60) % 60);
            var second = Math.floor(sys_second % 60);
            day_elem && $(day_elem).text(day);//计算天
            $(hour_elem).text(hour<10?"0"+hour:hour);//计算小时
            $(minute_elem).text(minute<10?"0"+minute:minute);//计算分
            $(second_elem).text(second<10?"0"+second:second);//计算秒
        } else {
            clearInterval(timer);
            $('.count_down_box').html('<span>本次活动已结束</span>');
        }
    }, 1000);
}

//错误提示;默认1.2s
function errorTipc(info,time){
    $('.error_tipc').text(info?info:'出错啦！').fadeIn().fadeOut(time?time:1200);
}