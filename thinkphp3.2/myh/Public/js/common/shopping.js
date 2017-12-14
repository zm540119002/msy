
		$.each($('input[class*=text_box]'),function(i,t){
			subtotal($(t));
		})
		$(function(){
		$(".add").click(function(){
			var t=$(this).parents().children('input[class*=text_box]');
			t.val(parseInt(t.val())+1);
			if(isNaN(t.val())){
				t.val(0);
			}
			subtotal(t)
		});
		$(".min").click(function(){ 
			var t=$(this).parents().children('input[class*=text_box]'); 
			t.val(parseInt(t.val())-1);
			if(parseInt(t.val())<0 || isNaN(t.val())){ 
				t.val(0);
			}
			subtotal(t);
		});
		$('input[class*=text_box]').keyup(function(){
			var t=$(this);
			t.val(parseInt(t.val()));
			if (isNaN(t.val()) || parseInt(t.val()) < 0) {
		        t.val(0);
		    }
		    if (t.val(parseInt(t.val())) != t.val()) {
		        t.val(parseInt(t.val()));
		    }
		    subtotal(t);
		});

		function subtotal(sum){
			var subtotal=0;
			subtotal=parseFloat(sum.closest(".price_wrap").find('.price').text())*parseInt(sum.val());
			sum.closest(".price_wrap").find('.subtotal').html(subtotal.toFixed(2));
			setTotal();
		}

		function setTotal(){
			var s=0; 
			$(".count").each(function(){ 
					s+=parseInt($(this).find('input[class*=text_box]').val())*parseFloat($(this).closest(".purch").find('.price').text()); 
				});
				$("#total").html(s.toFixed(2));
			} 
		});
