var fall='30011';
var trues='30010';
//点击弹框-确定按钮------选择支付方式
function gonext(obj){
	//选择金点还是现金 2现金 1金点
	var sign = $('.hidinp').attr('data-pay');
	var inpNum = $('.shownum').val();
	var id=$('.id').val();
	var mark=$(obj).attr('data');
	var d='sign='+sign+'&inpNum='+inpNum+'&id='+id;
	if(mark == 1){		//1购物车
		var u='insertCart';
		var f=function(res){
			$('.surebtn').remove("onclick");
			var response=eval(res);
			if(response['status'] == trues){
	    		window.location.assign(response['url']);
	    	}else{
	    		alert(response['msg']);
	    		$('.surebtn').attr("onclick","gonext()");
	    		return false;
	    	}
		}
		conv.httpRequest(u,d,f);
	}else{			//立刻购买
		//用户跳转页面-购物车1-确认订单2
		var state='1/'+$('.id').val()+'-'+$('.inpNum').val()+'-'+$('.sign').val()+';';
		$('.states').val(state);
		$('#shopHui').submit();
	}
}