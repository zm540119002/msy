(function($){
    //设置星星评分等级
    $.fn.setStar = function(options){
        var defaults={
            getFractionValue:1,
            mainCell:".star_img img",
            star: PUBLIC + '/images/sellerCompany/star.png',
            starRed: PUBLIC + '/images/sellerCompany/starred.png'
        };
        if($.isNumeric(options)){
            defaults.getFractionValue = options;
        }
        return this.each(function(){
            //console.log($(this));
            var _this=$(this);

            var opts=$.extend({},defaults,options);
            var starBox = $(opts.mainCell,_this);
            var getFractionValue = opts.getFractionValue;
            var star=opts.star;
            var starRed=opts.starRed;
            var starValue=parseInt(getFractionValue);
            starBox.each(function(index){
                //console.log(this);
                var prompt=['1分','2分','3分','4分','5分'];	//评价分数
                this.id=index;		//遍历img元素，设置单独的id
                starBox.attr('src',star);//空心星
                // _this.find('#'+parseInt(getFractionValue)).attr('src',starRed);		//当前的图片为实星
                // _this.find('#'+parseInt(getFractionValue)).prevAll().attr('src',starRed);	//当前的前面星星为实星
                // $(this).parent().next('span').text(prompt[getFractionValue]);
                // $(this).parent().next('span').attr('data-value',getFractionValue + 1);

                _this.find('#'+(starValue-1)).attr('src',starRed);		//当前的图片为实星
				_this.find('#'+(starValue-1)).prevAll().attr('src',starRed);	//当前的前面星星为实星  prompt[getFractionValue]
                $(this).parent().next('span').text(getFractionValue+'分');
                $(this).parent().next('span').attr('data-value',getFractionValue);
            });
        });
    };
    //获取星星评分等级
    $.fn.getStar = function(){
        return this.find('span').attr('data-value');
    };

    //星星评分（绝对定位布局）
     $.fn.setclassStar=function(options){
        var defaults={
            getFractionValue:1,
            mainCell:".real_star",
            star:'public/images/sellerCompany/star.png',
            starRed:'public/images/sellerCompany/starred.png'
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

    //设置进度条
    $.fn.setProgressBar=function(number){
        var defaults={
            mainCell:".real_star_progress"
        };
        if(!$.isNumeric(number)){
            number = 0;
        }
        return this.each(function(){
            var _this=$(this);
            var progressBox = $(defaults.mainCell,_this);
            progressBox.each(function(index){
                $(this).css('width',number+'%');
			});
        });
    };
})(jQuery);