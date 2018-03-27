var fall='30011';
var trues='30010';
//回收车弹框删除
function remFun(num){
	if(num == 1){
		$('.shadow,.last').hide();
		var idCount='';
		$('.list .rad_check').each(function(){
			idCount +=$(this).next().val()+';';
		});
		var u='delCart';
		var d='id='+idCount;
		var f=function(res){
			var response=eval(res);
			if(response['status'] == trues){
	    		window.location.assign(response['url']);
	    	}
		}
		conv.httpRequest(u,d,f);
	}else{
		$('.shadow,.last').hide();
	}
	$('body').removeClass('posi_fixed');
}
//结算1 删除2
function sCartFun(num){
	var numb = '';
	if(num == 1){
		if($('.all').is(':visible')){
		  //去结算 post  form 表单提交 字符串拼接
			var state='';
			$('.list .rad_check').each(function(){
				//获取选择回收的收取方式 1金点2钱
				var sign=0;
				var method=$(this).parent().next().find('.payrecy').html();
				if(method=="金点"){
					sign=1;
				}else if(method=="元"){
					sign=2;
				}
				//获取购物车id
				var packid=$(this).next('.packageid').val();
				//获取数量
				var num=$(this).parent().next().find('.shownum').attr('data');
				state +=packid+'-'+num+'-'+sign+';';
			});
			var ste='2/'+state;
			$('.states').val(ste);
			$('#cartOrder').submit();
		}else{
			alert('请选择您要结算的商品！');
		}
	}else{
		if($('.all').is(':visible')){
			$('.last_mess span').html($('.list .rad_check').length);
			$('.shadow').slideDown(50,function(){
				$('.last').show();
				$('body').addClass('posi_fixed');
			});
		}else{
			alert('请选择您要删除的商品！');
		}
	}
}