$(document).ready(function(){
	//列表下拉 v3-b12
	$('img[nc_type="flex"]').click(function(){
		var status = $(this).attr('status');
		if(status == 'open'){
			var pr = $(this).parent('td').parent('tr');
			var id = $(this).attr('fieldid');
			var obj = $(this);
			$(this).attr('status','none');
			//ajax
			$.ajax({
				url: 'goodsCategoryList/ajax/1/parent_id/'+id,
				dataType: 'json',
				success: function(data){
					var src='';
					if(data){
						for(var i = 0; i < data.length; i++){
							var tmp_vertline = "<img class='preimg' src='/public/admin-img/common/default/vertline.gif'/>";
							src += "<tr class='"+pr.attr('class')+" row"+id+"'>";
							if(data[i].deep==2){
								src += "<td class='w36'><input type='checkbox' name='check_id[]' value='"+data[i].id+"' class='checkitem type_two_level_menu'>";
							}else{
								src += "<td class='w36'><input type='checkbox' name='check_id[]' value='"+data[i].id+"' class='checkitem type_three_level_menu'>";
							}
							//图片
							if(data[i].have_child == 1){
								src += " <img fieldid='"+data[i].id+"' status='open' nc_type='flex' src='/public/admin-img/common/default/tv-expandable.gif' />";
							}else{
								src += " <img fieldid='"+data[i].id+"' status='none' nc_type='flex' src='/public/admin-img/common/default/tv-item.gif' />";
							}
							src += "</td><td class='w48 sort'>";
							//排序
							src += " <span title='可编辑下级分类排序' ajax_branch='goods_class_sort' datatype='number' fieldid='"+data[i].id+"' fieldname='sort' nc_type='inline_edit' class='editable tooltip'>"+data[i].sort+"</span></td>";
							//名称
							src += "<td class='w50pre name'>";


							for(var tmp_i=1; tmp_i < (data[i].deep-1); tmp_i++){
								src += tmp_vertline;
							}
							if(data[i].have_child == 1){
								src += " <img fieldid='"+data[i].id+"' status='open' nc_type='flex' src='/public/admin-img/common/default/tv-item1.gif' />";
							}else{
								src += " <img fieldid='"+data[i].id+"' status='none' nc_type='flex' src='/public/admin-img/common/default/tv-expandable1.gif' />";
							}
							src += " <span title='可编辑下级分类名称' required='1' fieldid='"+data[i].id+"' ajax_branch='goods_class_name' fieldname='gc_name' nc_type='inline_edit' class='editable tooltip'>"+data[i].name+"</span>";
							//新增下级
							
							src += "</td>";
							//是否显示
							/*src += "<td class='power-onoff'>";
							if(data[i].gc_show == 0){
								src += "<a href='JavaScript:void(0);' class='tooltip disabled' fieldvalue='0' fieldid='"+data[i].id+"' ajax_branch='goods_class_show' fieldname='gc_show' nc_type='inline_edit'><img src='"+ADMIN_TEMPLATES_URL+"/images/transparent.gif'></a>";
							}else{
								src += "<a href='JavaScript:void(0);' class='tooltip enabled' fieldvalue='1' fieldid='"+data[i].id+"' ajax_branch='goods_class_show' fieldname='gc_show' nc_type='inline_edit'><img src='"+ADMIN_TEMPLATES_URL+"/images/transparent.gif'></a>";
							}
							src += "</td>";
							*/

							//操作
							src += "<td class='w84'>";
							src += "<a href='GoodsCategory/goods_category_id/"+data[i].id+"'>编辑</a>";
							src += " | <a href=\"javascript:if(confirm('删除该分类将会同时删除该分类的所有下级分类，您确定要删除吗'))window.location = 'delGoodsCategory/goods_category_id/"+data[i].id+"';\">删除</a>";
							if(data[i].deep < 3){
								src += "| <a class='btn-add-nofloat marginleft' href='/index.php/Admin/PurchaseGoodsCategory/goodsCategory/id/"+data[i].id+"'><span>新增下级</span></a>";
							}
							src += "</td>";
							src += "</tr>";
						}
					}
					//插入
					pr.after(src);
					obj.attr('status','close');
					obj.attr('src',obj.attr('src').replace("tv-expandable","tv-collapsable"));
					$('img[nc_type="flex"]').unbind('click');
					$('span[nc_type="inline_edit"]').unbind('click');
					//重现初始化页面
                    $.getScript("/public/admin-js/purchase/edit.js");
					$.getScript("/public/admin-js/purchase/jquery.goods_class.js");
					
				},
				error: function(){
					alert('获取信息失败');
				}
			});
		}
		if(status == 'close'){
			$(".row"+$(this).attr('fieldid')).remove();
			$(this).attr('src',$(this).attr('src').replace("tv-collapsable","tv-expandable"));
			$(this).attr('status','open');
		}
	})
});