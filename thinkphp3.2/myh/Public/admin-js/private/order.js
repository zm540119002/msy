//商品列表
function getPage(currentPage) {
    $("#order_list").html($('#loading').html());
    var url = CONTROLLER+'/orderList';
    var postData = $('#form1').serializeObject();
    postData.p = currentPage ? currentPage : 1;
    postData.pageSize = 10;
    $.get(url, postData , function(data){
        $('#order_list').html(data);
    });
}

$(function(){
    //加载第一页
    getPage();
    //选择订单类型
    $('body').on('change','.multiselect',function () {
        getPage();
    });

    //翻页
    $('body').on('click','.pager a',function(){
        getPage($(this).data('page'));
    });

    //搜索
    $('.order_search').click(function(){
        getPage();
    });

    //编辑
    $('body').on('click','.editGoods',function(){
        var _thisTr = $(this).parents('tr');
        var url =  CONTROLLER + '/goodsBaseEdit/goodsBaseId/' + _thisTr.data('id');
        location.href = url;
    });

    //删除
    $('body').on('click','.delGoods',function(){
        var _thisTr = $(this).parents('tr');
        var postData = {};
        postData.goodsBaseId = _thisTr.data('id');
        var url =  CONTROLLER + '/goodsBaseDel';
        $.post(url,postData,function(msg){
            dialog.msg(msg,'',function(){
                _thisTr.remove();
            });
        });
    });

    //批量删除
    $('body').on('click','.batchDel',function(){
    });
    //去发货提交
    var deliverLayerForm=$('.deliverLayerForm').html();
    $('body').on('click','.deliver_goods',function(){
        var _this = $(this);
        layer.open({
            skin: 'remarksLayer',
            area: ['600px', '360px'],
            title:'订单信息',
            content:deliverLayerForm,
            btn:['确定','取消'],
            success:function () {

            },
            yes:function (index) {
                var url = MODULE+'/Logistics/LogisticsEdit'
                var postData = $('.remarksLayer #undertake').serializeObject();
                postData.orderId = _this.parents('tr').data('order_id');
                $.post(url,postData,function(msg){
                    if(msg.status == 1){
                        location.reload();
                    }else{
                        dialog.error(msg.info)
                    }
                });
                layer.close(index);
            }
        })
    });
   
});