var fall='30011';
var trues='30010';
//获取当前页面url后面的方法及参数名
function getUrlPara(){
	var dd='';
	var urls='';
	var urlPa=window.location.pathname;
	var param=window.location.search;//获取属性的内容
	dd=urlPa.substring(urlPa.lastIndexOf('\/')+1);
	urls=dd+param;
	$('.noaddr a').attr('href','addreslists?state='+urls);
	$('.addr_tit .ad_top').attr('href','addreslists?state='+urls);
	//return urls;
}
//校验参数
function checkData(){
	//alert('a');
	var option='';
	$('.allshop .shop').each(function(){
		var reg = new RegExp("^[1-9]*$"); 
		var packid=$(this).children('.shop_m').find('.packid').val();
		if(!reg.test(packid)){
			alert('提交订单出现异常!');
			return false;
		}
		option = option + packid+',';
		//alert(option);
	});
	return option;
}
//提交订单前检查地址是否存在
function checkaddr(){
	var addressId=$('.addressId').val();
	if(addressId==''){
		alert('请添加地址');
		return false;
	}else{
		$('#shopOrder').submit();
	}
}
//添加商城订单
function insertOrder(){
	var addrId=$('.addressId').val();
	var method=$('.hidPay').val();
	var d='addrId='+addrId+'&packid='+checkData()+'&method='+method;
	var u='sureOrder';
	var f=function(res){
			var response=eval(res);
			if(response['status'] == trues){
				window.location.assign('intoJdpay?orderId='+response['data']);
			}else{
				alert(response['msg']);
				$('.sureBtn').attr("onclick","insertOrder()");
				return false;
			}
		}
	conv.httpRequest(u,d,f);
}
//商城购买东西 输入密码支付 验证密码
function checkPayPwd(){
	var orderId= conv.getUrlParam('orderId');
	var pwd=$('.passwd').val();
	if(pwd==''){
		alert('信息不能为空');
		return false;
	}
	var u='checkPayPwd';
	var d='orderId='+orderId+'&pwd='+pwd;
	var f=function(res){
		var response=eval(res);
		if(response['status'] == trues){
			window.location.assign(response['url']);
    	}else{
    		alert(response['msg']);
    		$('.sureBtn').attr("onclick","checkPayPwd()");
    		return false;
       	}
	}
	conv.httpRequest(u,d,f);
}