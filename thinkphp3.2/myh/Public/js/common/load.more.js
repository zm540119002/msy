/**
 * Created by Administrator on 2017/4/17.
 * 加载更多js
 */
;(function(w,$){
    if(!$){
        return false;
    }
    var loadMore = {
        /*单页加载更多 通用方法
         * @param callback 回调方法
         * @param config 自定义参数
         */
        get : function(callback, config){
            var config = config ? config : {}; /*防止未传参数报错*/

            var counter = 0; /*计数器*/
            var currentPage = 0; //当前页
            var pageSize = config.pageSize ? config.pageSize : 5; //每页显示个数

            /*默认通过点击加载更多*/
            $(document).on('click', '.js-load-more', function(){
                counter ++;
                currentPage = counter * pageSize;

                callback && callback(config, currentPage, pageSize);
            });

            /*通过自动监听滚动事件加载更多,可选支持*/
            config.isEnd = false; /*结束标志*/
            config.isAjax = false; /*防止滚动过快，服务端没来得及响应造成多次请求*/
            $(window).scroll(function(){
                /*是否开启滚动加载*/
                if(!config.scroll){
                    return false;
                }

                /*滚动加载时如果已经没有更多的数据了、正在发生请求时，不能继续进行*/
                if(config.isEnd == true || config.isAjax == true){
                    return false;
                }

                /*当滚动到最底部以上多少像素时， 加载新内容，默认为0*/
                config.bottomHeight ? config.bottomHeight : 0;
                if ($(document).height() - $(this).scrollTop() - $(this).height() < config.bottomHeight){
                    counter ++;
                    currentPage = counter * pageSize;
                    callback && callback(config, currentPage, pageSize);
                }
            });

            /*第一次自动加载*/
            callback && callback(config, currentPage, pageSize);
        }
    };
    $.loadMore = loadMore;
})(window,window.jQuery);

/** 使用示例
 $.loadMore.get(getData, {
     scroll: true,  //默认是false,是否支持滚动加载
     pageSize:7,  //默认是10
     flag: 1, //自定义参数，可选，示例里没有用到
 });

function getData(config, offset,pageSize){

    config.isAjax = true;

    $.ajax({
        type: 'GET',
        url: 'json/blog.json',
        dataType: 'json',
        success: function(reponse){

            config.isAjax = false;

            var data = reponse.list;
            var sum = reponse.list.length;

            var result = '';

            //业务逻辑块：实现拼接html内容并append到页面

            //console.log(offset , pageSize, sum);

            // 如果剩下的记录数不够分页，就让分页数取剩下的记录数
            // 例如分页数是5，只剩2条，则只取2条
            // 实际MySQL查询时不写这个

            if(sum - offset < pageSize ){
                pageSize = sum - offset;
            }


            //使用for循环模拟SQL里的limit(offset,pageSize)
            for(var i=offset; i< (offset+pageSize); i++){
                result +='<div class="weui_media_box weui_media_text">'+
                    '<a href="'+ data[i].url +'" target="_blank"><h4 class="weui_media_title">'+ data[i].title +'</h4></a>'+
                    '<p class="weui_media_desc">'+ data[i].desc +'</p>'+
                    '</div>';
            }

            $('.js-blog-list').append(result);

            //隐藏more
            if ( (offset + pageSize) >= sum){
                $(".js-load-more").hide();
                config.isEnd = true; //停止滚动加载请求
                //提示没有了
            }else{
                $(".js-load-more").show();
            }
        },
        error: function(xhr, type){
            alert('Ajax error!');
        }
    });
}

*/