$(document).ready(function(){
    //初始化一级菜单
    $('[name=goods_category_id_1]').append($('#allGoodsCategoryListTemp').find('option[level=1]').clone());
    //一级菜单change
    $('[name=goods_category_id_1]').change(function(){
        //首先清空二级菜单和三级菜单
        $('[name=goods_category_id_2],[name=goods_category_id_3]').find('option[value!=""]').remove();
        //然后二级菜单追加所选一级菜单的子菜单
        if($(this).val()){
            var childMenu = $('#allGoodsCategoryListTemp').find('option[level=2][parent_id_1='+$(this).val()+']');
            $('[name=goods_category_id_2]').append(childMenu.clone());
        }
    });
    //二级菜单change
    $('[name=goods_category_id_2]').change(function(){
        //首先清空三级菜单
        $('[name=goods_category_id_3]').find('option[value!=""]').remove();
        //然后三级菜单追加所选二级菜单的子菜单
        if($(this).val()){
            var childMenu = $('#allGoodsCategoryListTemp').find('option[level=3][parent_id_2='+$(this).val()+']');
            $('[name=goods_category_id_3]').append(childMenu.clone());
        }
    });

    //分类筛选
    $('body').on('change','[name=goods_category_id_1],[name=goods_category_id_2],[name=goods_category_id_3]',function () {
        getPage();
    });

    //翻页
    $('body').on('click','.pager a',function(){
        getPage($(this).data('page'));
    });
});