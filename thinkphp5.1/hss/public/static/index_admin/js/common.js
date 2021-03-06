$(function () {
    //加载第一页
    var getData = $('#form1').serializeObject();
    getPagingList(config, getData);
});
var config = {url: controller + 'getList'};
var title = $("#title").attr('title');

//翻页
$('body').on('click', '.pager2', function () {
    var curIndex = $(this).parents('ul.pagination').find('li.active span').text();
    var selectedPage = $(this).data('page');
    if (selectedPage == '»') {
        curIndex++;
        selectedPage = curIndex;
    }
    if (selectedPage == '«') {
        curIndex--;
        selectedPage = curIndex;
    }
    config.currentPage = selectedPage;

    var getData = $('#form1').serializeObject();

    getPagingList(config, getData);
});

// 搜索 STATUS
$('body').on('click','#search',function(){
    search();
});
$('.keyword').bind('keypress', function () {
    if (event.keyCode == "13") {
        search();
        return false;
    }
});
function search() {
    var getData = $('#form1').serializeObject();
    getPagingList(config, getData);
}
// 搜索 END

// 管理关联的商品
$('body').on('click','.relationGoods',function(){
    var _thisTr = $(this).parents('tr');
    var url =  controller + 'manageRelationGoods/id/' + _thisTr.data('id');

    layer.open({
        type: 2,
        title:'添加商品',
        shadeClose: false,
        skin: 'layui-layer-demo',
        shade: 0.8,
        area: ['80%', '90%'],
        content: url //iframe的url
    });
});

// 预览
function preview(url){
    layer.open({
        type: 2,
        maxmin: true,
        shade: false,
        shadeClose: false,
        area: ['300px','440px'],
        content: url
    });
}
