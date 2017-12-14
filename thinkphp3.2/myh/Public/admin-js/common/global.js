
   
/**
 * 获取多级联动的商品分类
 */
function get_category(id,next,select_id){
    var url = '/index.php/Admin/GoodsCategory/getCategory/parent_id/'+ id;
    $.ajax({
        type : "GET",
        url  : url,
        error: function(request) {
            dialog.error("服务器繁忙, 请联系管理员!");
        },
        success: function(v) {
            if(v.length==0 ){
                 if(!id){
                     $('#parent_id_2 option:gt(0)').remove();
                 }
                 $('#parent_id_3 option:gt(0)').remove();

            }else{
                v = "<option value='0'>请选择商品分类</option>" + v;
                $('#'+next).empty().html(v);
                (select_id > 0) && $('#'+next).val(select_id);//默认选中
            }

        }
    });
}

