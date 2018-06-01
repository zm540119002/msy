/**
 * Created by Administrator on 2018/5/25.
 */
//获取组织列表
function getOrganizeList() {
    var postData = {};
    var url = module + 'Organize/getOrganizeList';
    $.get(url,postData,function(data){
        $.each(data,function(i,o){
            $.each($('section.organize').find('div.operate-box'),function(){
                if($(this).parent().parent().data('id')==o.superior_id){
                    var node = $('#organize_tpl').find('ul').clone();
                    node.attr('data-id',o.id);
                    node.attr('data-superior_id',o.superior_id);
                    node.attr('data-level',o.level);
                    node.find('span').text(o.name);
                    $(this).after(node);
                }
            });
        });
        $('#setOrganize').find('div').remove();
        setOrganize=$('#setOrganize').html();
    });
}

//获取员工账号列表
function getAccountList() {
    var postData = {};
    postData.keyword = $('[name=keyword]').val();
    var url = module + 'Account/getAccountList';
    $.get(url,postData,function(data){
        if(data.status==0){
            dialog.error(data.info);
        }else{
            $('ul.account-list').empty().append(data);
        }
    });
}