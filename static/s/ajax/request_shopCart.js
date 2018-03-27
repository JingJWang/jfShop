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
				alert('删除成功');
	    		window.location.assign(response['url']);
	    	}
		}
		conv.httpRequest(u,d,f);
	}else{
		$('.shadow,.last').hide();
	}
}
//结算1 删除2
function sCartFun(num){
	var numb = '';
	if(num == 1){
		if($('.all').is(':visible')){
		  //去结算
			var idCount='';//id和提交个数拼接
			$('.list .rad_check').each(function(){
				idCount +=$(this).next().val()+',';
			});
			window.location.assign("confirmMany?id="+idCount);
		}else{
			alert('请选择您要结算的商品！');
		}
	}else{
		if($('.all').is(':visible')){
			$('.last_mess span').html($('.list .rad_check').length);
			$('.shadow').slideDown(50,function(){
				$('.last').show();
			});
		}else{
			alert('请选择您要删除的商品！');
		}
	}
}