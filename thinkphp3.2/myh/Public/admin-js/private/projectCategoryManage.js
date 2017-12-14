$(document).ready(function(){
    //折叠
    $('body').on('click','.folding',function(){
        var _this = $(this);
        var status = _this.attr('status');
        var _thisTr = _this.parents('tr');
        if(status == 'open'){
            _this.attr('status','close');
            var postData = {};
            postData.level = _thisTr.data('level');
            postData.projectCategoryId = _thisTr.data('id');
            postData.parent_id_1 = _thisTr.data('parent-id-1');

            //异步加载子分类
            $.ajax({
                url: 'projectCategoryManage',
                type:'post',
                data:postData,
                dataType: 'html',
                error: function(){
                    dialog.error('AJAX错误。。。');
                },
                success: function(data){
                    _thisTr.after(data);
                }
            });
        }else if(status == 'close'){
            _this.attr('status','open');
            if(_thisTr.data('level') == 1){
                _thisTr.nextUntil('[data-level=1]').remove();
            }else if(_thisTr.data('level') == 2){
                _thisTr.nextUntil('[data-level!=3]').remove();
            }
        }
    });

    //编辑
    $('body').on('click','.a-edit',function(){
        var _thisTr = $(this).parents('tr');
        var url = CONTROLLER + '/projectCategoryEdit';
        url += '/projectCategoryId/' + _thisTr.data('id');
        url += '/operate/' + 'edit';
        location.href = url;
    });

    //删除
    $('body').on('click','.a-del',function(){
        var _thisTr = $(this).parents('tr');
        var postData = {};
        postData.projectCategoryId = _thisTr.data('id');
        var url = CONTROLLER + '/delProjectCategory';

        var info = '删除该分类将会同时删除该分类的所有下级分类，您确定要删除吗';
        //询问框
        parent.layer.confirm(info, {
            btn: ['确定','取消'] //按钮
        }, function(){
            $.post(url,postData,function(msg){
                if(msg.status ==1){
                    parent.layer.msg(msg.info,{time:1000},function(){
                        var level = _thisTr.data('level');
                        if(level == 1){
                            _thisTr.nextUntil('[data-level=1]').remove();
                        }else if(level == 2){
                            _thisTr.nextUntil('[data-level!=3]').remove();
                        }
                        _thisTr.remove();
                    });
                }else {
                    parent.layer.msg(msg.info,{time:3000});
                }
            });
        });
    });

    //新增下级
    $('body').on('click','.a-add',function(){
        var _thisTr = $(this).parents('tr');
        var url = CONTROLLER + '/projectCategoryEdit';
        url += '/projectCategoryId/' + _thisTr.data('id');
        url += '/operate/' + 'addLower';
        location.href = url;
    });

    //批量删除
    $('body').on('click','.a-del-batch',function(){
        // var url = '';
        // if(dialog.confirm('删除该分类将会同时删除该分类的所有下级分类，您确定要删除吗'))
        //     location.href = url;
    });
});