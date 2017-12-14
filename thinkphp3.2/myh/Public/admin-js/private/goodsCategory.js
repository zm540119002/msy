$(document).ready(function(){
    //折叠
    $('body').on('click','.folding',function(){
        var _this = $(this);
        var status = _this.attr('status');
        var _thisTr = _this.parents('tr');
        var checkBox;
        if(_thisTr.find('.checkitem').prop('checked')){
            checkBox = true;
        }
        var level = _thisTr.data('level');

        if(status == 'open'){
            _this.attr('status','close');
            var postData = {};
            postData.level = _thisTr.data('level');
            postData.goodsCategoryId = _thisTr.data('id');
            postData.parentId = _thisTr.data('parent-id');

            //异步加载子分类
            $.ajax({
                url: 'goodsCategoryList',
                type:'post',
                data:postData,
                dataType: 'html',
                error: function(){
                    dialog.error('AJAX错误。。。');
                },
                success: function(data){
                    _thisTr.after(data);
                    if(checkBox){
                        if(level == 1){
                            $.each( _thisTr.nextUntil('[data-level = 1]').find('.checkitem'),function () {
                                $(this).prop('checked',true);
                            });
                        }
                        if(level == 2){
                            $.each( _thisTr.nextUntil('[data-level != 3]').find('.checkitem'),function () {
                                $(this).prop('checked',true);
                            });
                        }
                    }
                }
            });
        }else if(status == 'close'){
            _this.attr('status','open');
            if(_thisTr.data('level') == 1){
                _thisTr.nextUntil('[data-level = 1]').remove();
            }else if(_thisTr.data('level') == 2){
                _thisTr.nextUntil('[data-level!=3]').remove();
            }
        }
    });

    //编辑
    $('body').on('click','.a-edit',function(){
        var _thisTr = $(this).parents('tr');
        var id = _thisTr.data('id');
        var parent_id = _thisTr.data('parent-id');
        var url = CONTROLLER + '/editGoodsCategory' + '/categoryId/' + id ;
        location.href = url;
    });
    //新增下级
    $('body').on('click','.a-add',function(){
        var _thisTr = $(this).parents('tr');
        var id = _thisTr.data('id');
        var parent_id = _thisTr.data('parent-id');
        var url = CONTROLLER + '/editGoodsCategory' + '/id/' + id ;
        location.href = url;
    });
    //删除
    $('body').on('click','.a-del',function(){
        var _thisTr = $(this).parents('tr');
        var categoryId = _thisTr.data('id');
        var categoryIds = [];
        categoryIds[0] = categoryId;
        parent.layer.open({
            btn: ['确定','取消'],//按钮
            content:'请注意有可能分类下面还有子分类，删除可能会导致子分类也删除，确认删除吗？',
            yes:function (index) {
                $.post('delGoodsCategory',{categoryIds:categoryIds},function(msg){
                    if(msg.status == 0){
                         dialog.error(msg.info);
                    }
                    if(msg.status == 1){

                        if(_thisTr.data('level') == 1){
                            _thisTr.nextUntil('[data-level = 1]').remove();
                            _thisTr.remove();
                        }else if(_thisTr.data('level') == 2){
                            _thisTr.nextUntil('[data-level !=3]').remove();
                            _thisTr.remove();
                        }else if(_thisTr.data('level') == 3){
                            _thisTr.remove();
                        }
                    }
                });
                parent.layer.close(index);
            }
        })

    });

    //批量删除
    $('body').on('click','.a-del-batch',function(){
        var url = '';
        var categoryIds =[];
        $("input[class='checkitem']").each(function(){
            if($(this).is(':checked')){
                categoryIds.push($(this).parent().parent().data('id'));
            }
        });
        if(categoryIds.length == 0){
            layer.tips('你还没有选择任何内容！','.a-del-batch',{
                tips:[2,'#0d7eff'],
                time:1500
            });
            return false;
        }else{
            parent.layer.confirm('请注意有可能分类下面还有子分类，删除可能会导致子分类也删除，确认删除吗？', {
                btn: ['确定','取消'],//按钮
                yes:function (index) {
                    $.post('delGoodsCategory',{categoryIds:categoryIds},function(msg){
                        if(msg.status == 0){
                            dialog.error(msg.message);
                        }
                        if(msg.status == 1){
                            $("input[class='checkitem']").each(function(){
                                if($(this).is(':checked')){
                                    var _thisTr = $(this).parents('tr');
                                    if(_thisTr.data('level') == 1){
                                        _thisTr.nextUntil('[data-level = 1]').remove();
                                        _thisTr.remove();
                                    }else if(_thisTr.data('level') == 2){
                                        _thisTr.nextUntil('[data-level !=3]').remove();
                                        _thisTr.remove();
                                    }else if(_thisTr.data('level') == 3){
                                        _thisTr.remove();
                                    }

                                }
                            })
                        }
                    },'JSON');
                    parent.layer.close(index);
                }
            })
        }
    });

    //全选
    $('body').on('click','.checkitem',function () {
        var _this = $(this);
        var _thisTr = _this.parents('tr');
        var level = _thisTr.data('level');
        var _thisChecked = $(this).prop("checked");

        if(level == 1){
            $.each( _thisTr.nextUntil('[data-level = 1]').find('.checkitem'),function () {
                $(this).prop('checked',_thisChecked);
            });
        }
        if(level == 2){
            $.each( _thisTr.nextUntil('[data-level != 3]').find('.checkitem'),function () {
                $(this).prop('checked',_thisChecked);
            });
        }
    });

//反选
    $('body').on('click','.checkitem',function () {
        var sign = true;
        var sign1 = true;
        var sign2 = true;
        var sign3 = true;
        var _this = $(this);
        var _thisTr = _this.parents('tr');
        var level = _thisTr.data('level');

        //一票否决
        if(level == 3){

            if(!$(this).prop('checked')){
                sign1 = false;
            }

            $.each( _thisTr.nextUntil('[data-level != 3]').find('.checkitem'),function () {
                if(!$(this).prop('checked')){
                    sign2 = false;
                }
            });

            $.each( _thisTr.prevUntil('[data-level != 3]').find('.checkitem'),function () {
                if(!$(this).prop('checked')){
                    sign3 = false;
                }
            });
            sing=(sign1 && sign2 &&sign3);
            if(!sing){
                _thisTr.prevAll('[data-level = 1]').eq(0).find('.checkitem').prop('checked',sing);
            }
            _thisTr.prevAll('[data-level = 2]').eq(0).find('.checkitem').prop('checked',sing);
        }


        if(level == 2){

            if(!$(this).prop('checked')){
                sign1 = false;
            }

            $.each( _thisTr.nextUntil('[data-level = 1]').find('.checkitem'),function () {
                if(!$(this).prop('checked')){
                    sign2 = false;
                }
            });

            $.each( _thisTr.prevUntil('[data-level = 1]').find('.checkitem'),function () {
                if(!$(this).prop('checked')){
                    sign3 = false;
                }
            });
            sign=(sign1&&sign2&&sign3);
             _thisTr.prevAll('[data-level = 1]').eq(0).find('.checkitem').prop('checked',sign);
        }

    });

});