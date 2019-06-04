var title = $("#title").attr('title');

// 增加 OR 编辑
$('body').on('click','.add',function(){
    var config  = {};
    config.title= '添加'+title;
    config.url  = controller + 'edit';

    var pid = $(this).data('pid');

    if( pid>0&&pid!='undefined' ){
        config.url = config.url+'/pid/'+pid;
    }

    edit(config);
});
$('body').on('click','.edit',function(){

    var _thisTr = $(this).parents('tr');
    var config  = {};
    config.title= title+'编辑';
    config.url  =  controller + 'edit/id/' + _thisTr.data('id');

    var pid = $(".add").data('pid');

    if( pid>0&&pid!='undefined' ){
        config.url = config.url+'/pid/'+pid;
    }

    edit(config);
});
function edit(config){
    var index = layer.open({
        type: 2,
        title: config.title,
        content: config.url
    });
    layer.full(index);
}

//删除
$('body').on('click','.del',function(){
    var _thisTr = $(this).parents('tr');
    var postData = {};
    postData.id = _thisTr.data('id');
    var url =  controller + 'del';
    layer.open({
        btn: ['确定','取消'],//按钮
        content:'确定要删除'+title+' 编号'+postData.id+'？',
        yes:function (index) {
            $.post(url,postData,function(msg){
                if(msg.status){
                    dialog.msg(msg,'',function(){
                        _thisTr.remove();
                    });
                }
            });
            layer.close(index);
        }
    });
});
//批量删除数据
$('body').on('click','.batchDel',function(){
    var postData = {};
    ids = [];

    var checked = $("input[name='checkbox']:checked");

    checked.each(function(){
        var _thisTr = $(this).parents('tr');
        ids.push(_thisTr.data('id'));
    });

    postData.ids= ids;
    var url =  controller + '/del';

    if(ids.length==0){
        layer.tips('请选择要删除的'+title+'！','.btn',{
            tips:[4,'#0d7eff'],
            time:1500
        });
        return false;
    }else{
        layer.confirm('确定要删除选中的'+title+'？', {
            btn: ['确定','取消'],//按钮
            yes:function (index) {
                $.post(url,postData,function(msg){
                    dialog.msg(msg,'',function () {
                        if(msg.status){
                            /*       $.each(ids, function(i,val){
                             $('tr[data-id='+val+']').remove();
                             });*/

                            //$("input[name='checkbox']:checked").each(function(){
                            checked.each(function(){
                                $(this).parents('tr').remove();
                            });
                        }
                    });
                });
                layer.close(index);
            }
        })
    }
})
//上下架
$('body').on('click','.set-shelf-status',function(){
    var _this = $(this);
    //var text = _this.attr('title');
    var text = _this.text();
    var shelf_status = _this.data('shelf-status');
    var _thisTr = $(this).parents('tr');
    var postData ={};
    postData.id = _thisTr.data('id');
    postData.shelf_status = shelf_status;
    var url =  controller + 'setInfo';
    layer.open({
        btn: ['确定','取消'],//按钮 做到这里
        content:text+' ?',
        yes:function (index) {
            $.post(url,postData,function(msg){
                dialog.msg(msg,'',function(){
                    if(msg.status){

                        if(shelf_status == 3){
                            //_thisTr.find(".shelf-status").html('<span class="label label-defaunt radius">已下架</span>');
                            _this.data('shelf-status',1);
                            _this.attr('title','上架');
                            _this.html('上架');

                        }else{
                            //_thisTr.find(".shelf-status").html('<span class="label label-success radius">已上架</span>');
                            _this.data('shelf-status',3);
                            _this.attr('title','下架');
                            _this.html('下架');
                        }
                    }
                });
            });
            layer.close(index);
        }
    });
});

// 预览
$('body').on('click','.preview',function(){
    var index = parent.layer.getFrameIndex(window.name);
    parent.layer.iframeAuto(index);
    var _thisTr = $(this).parents('tr');
    var url =  '/index/Promotion/detail/id/' + _thisTr.data('id');
    layer.open({
        type: 2,
        maxmin: true,
        shade: false,
        area: ['500px','700px'],
        title: '项目预览',
        content: url
    });
});