var fall='30011';
var trues='30010';
//直接购买
function checkShopId(){
	var shopid=$('.shopid').val();
	if(shopid == ''){
		alert("非法数据");
		return false;
	}
	var inpNum=$('.shownum').attr('data');
	var u='confirmOrder';
	var d='state=1&shopid='+shopid+'&inpNum='+inpNum;
	var f=function(res){
		var response=eval(res);
		if(response['status'] == trues){
			window.location.assign(response['url']);
    	}else{
    		alert(response['msg']);
    		$('.gshop').attr("onclick","checkShopId()");
    		return false;
    	}
	}
	conv.httpRequest(u,d,f);
}
//添加购物车
function joinCart(){
	var shopid=$('.shopid').val();
	var inpNum=$('.shownum').attr('data');
	var busineid=$('.busineid').val();
	var u='addshopcart';
	var d='shopid='+shopid+'&inpNum='+inpNum+'&busineid='+busineid;
	var f=function(res){
		var response=eval(res);
		if(response['status'] == trues){
			window.location.reload();
    		alert('已成功加入购物车');
    	}else{
    		alert(response['msg']);
    		$('.addshopcart').attr("onclick","joinCart()");
    		return false;
    	}
	}
	conv.httpRequest(u,d,f);
}

function fromHui(){
	$('#formHui').submit();
}