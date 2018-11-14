//选择管理店铺
function switchManagerStore(){
    var content = $('#storeShopInfo').html();
    layer.open({
        title:['选择店家店铺进入','border-bottom:1px solid #d9d9d9;'],
        className:'storeShopListLayer',
        type: 1,
        fixed:true,
        // top:100,
        shadeClose:false,
        content:content,
        anim: 'up',
        style: '',
        success:function(){
            var winHeight=$(window).height();
            $('.storeShopListLayer .store_list').css('height',winHeight-120+'px');
            $('.layui-m-layer .layui-m-layermain').addClass('arrow-bottom');
            $('.layui-m-layermain .layui-m-layersection').addClass('bottom-layer');
        },
        btn:['确定','取消'],
        yes:function(index){
            var storeId = $('.storeShopListLayer li.current').data('id');
            if(!storeId){
                dialog.error('请选择店家店铺');
                return false;
            }
            var url = controller + 'manage/storeId/' +storeId;
            location.href = url;
        }
    });
}