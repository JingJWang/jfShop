function showtk(obj){
	var userid=$('.userid').val();
	var widh = $(window).height();
	$('.shadow').height(widh);
	$('html,body').addClass('ovfHiden'); //使网页不可滚动
	//弹框遮，罩层显示
	$('.shadow,.jy').show();
	//确定弹框跳转页面-购物车-立刻购买
	$('.surebtn').attr('data',$(obj).attr('data'));
	//验证点击购物车时 是否已经登录 如果没有登录就直接跳转到购物车
	if(userid=='' && $('.surebtn').attr('data')==1){
		$('.surebtn').attr('onclick','huicart()');
	}
}
function closefun(){
	$('.jy,.shadow').hide();
	$('html,body').removeClass('ovfHiden'); //使网页滚动
}
//点击切换支付方式
//num 1 金点   
//num 2 现金
function radcheck(obj,num){
	$(obj).parent().find('.rad').addClass('rad_check').parent().siblings().find('.rad').removeClass('rad_check');
	$('.hidinp').attr('data-pay', num);
	$('.sign').val(num);
}
//点击弹框-确定按钮
/*function gonext(obj){
	var gonex = $(obj).attr('data');
	//用户跳转页面-购物车1-确认订单2
	if(gonex == 1){
		location.href = "../../view/r/shopCart.html";
	}else{
		location.href = "../../view/r/order.html";
	}
	$('.jy,.shadow').hide();
}*/
//数量加减
function numFun(obj,num){	
	var inpNum = $('.shownum').val();
	if(num == 1){
		inpNum++;
	}else{
		if(inpNum == 1){
			inpNum == 1;
		}else{
			inpNum--;
		}
	}
	$('.shownum').val(inpNum);
	$('.shownum').attr('data',inpNum);
	$('.inpNum').val(inpNum);
}