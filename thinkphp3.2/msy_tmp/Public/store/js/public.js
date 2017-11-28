// (function(){
// var deviceWidth=document.documentElement.clientWidth;
// var html =document.getElementsByTagName('html')[0];
// html.style.fontSize=deviceWidth/6.4+'px';



// })()
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
		}
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
			})
		})
	}
})(jQuery);

//限制input、textarea字数
var ObjMaxLength={
	MaximumWord:function(obj,max){
		var val=$(obj).val().length;
		var content='最多只能输入'+max+'个字';
			if(val>max){
				layer.open({
					content:content,
					time:2
				})
				$(obj).val($(obj).val().substring(0,max));
			}
	}
}