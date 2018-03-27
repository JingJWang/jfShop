//支付方式
function changeHov(obj){
	$(obj).find('.pl_tit').addClass('checkH').parent().siblings().find('.pl_tit').removeClass('checkH');
	$('.hidPay').attr('data',$(obj).attr('data'));
	$('.hidPay').val($(obj).attr('data'));
	//计算总计
	jdFun();
}
//合计
function jdFun(){
	//总价  1金点   2金钱
	var allnum = '';
	var inte=0;
	var pri=0;
	$('.shop .s_price').each(function(){
		var num=$(this).parent().children('.num').val();
		var stNum = $('.hidPay').attr('data');
		if(stNum == 1){
			$('.dw_zj').html('金点');
			var a_num = $(this).find('.integral').html();	//取每个金点值
			var anum = parseFloat(a_num)*num*1000;
			inte = Math.round(parseFloat(inte)*1000+Number(anum))/1000;
		}else{
			$('.dw_zj').html('元');
			var a_num = $(this).find('.price').html();	 //取每个商品的元
			var anum = parseFloat(a_num)*100*num*1000;
			pri = Math.round(parseFloat(pri)*1000 + anum)/1000;
		}
	})
	if( pri != 0 ){
		allnum = pri /100;
	}else{
		allnum = inte;
	}
	$('.num_zj').html(allnum);	//最后的值
}