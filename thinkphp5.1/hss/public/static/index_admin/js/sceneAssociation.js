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
    getPagingList(config,getData);
});

// 取消促销关联
function delRelationPromotion(_this,postData){

    var _thisTr   = _this.parents('tr');
    postData.id   = _thisTr.data('id');
    $(this).addClass('nodisabled');
    layer.open({
        btn: ['确定','取消'],//按钮
        content:'取消关联选中的'+postData.title+' 编号'+postData.id+'？',
        yes:function (index) {
            $.ajax({
                url:module+'Promotion/delRelationPromotion',
                data:postData,
                method:'post',
                beforeSend:function(){

                },
                success:function(msg){
                    dialog.msg(msg,'',function(){
                        _thisTr.remove();
                    });
                    layer.close(index);
                },

            })
        },
        end:function(){
            _this.removeClass('nodisabled');
        }
    });
}