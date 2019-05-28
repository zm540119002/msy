
function getList(){
    //加载第一页
    var config = {
        url:controller+'getList'
    };
    var getData = $('#form1').serializeObject();
    getPagingList(config,getData);

//翻页
    $('body').on('click','.pager2',function(){
        var curIndex= $(this).parents('ul.pagination').find('li.active span').text();
        var selectedPage=$(this).data('page');
        if(selectedPage=='»'){
            curIndex++;
            selectedPage=curIndex;
        }
        if(selectedPage=='«'){
            curIndex--;
            selectedPage=curIndex;
        }
        config.currentPage = selectedPage;

        var getData = $('#form1').serializeObject();

        getPagingList(config,getData);
    });
}

//搜索
$('#search').click(function(){
    //config.currentPage = 0;
    var getData = $('#form1').serializeObject();
    getPagingList(config,getData);
});