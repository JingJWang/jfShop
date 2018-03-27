function hideShad(){
	$('.shad,.shadow').hide();
}
function showShad(id){
	$('.shopid').val(id);
	$('.shad,.shadow').show();
}
function radCheck(obj){
	$(obj).addClass('check_rad').siblings('.radio').removeClass('check_rad');
	var integral = $('.tj_d_price').val();//金点值
	var price = $('.tj_q_price').val(); //现金
	var start=0;	//区间 最小值
	var end=0;		//区间 最大值
	if($(obj).attr('data') == 1){
		$('.danw').html('金点');
		start=parseFloat(integral/1000*8);
		end=parseFloat(integral/1000*12);
	}else{
		$('.danw').html('元');
		start=parseFloat(price/1000*8);
		end=parseFloat(price/1000*12);
	}
	$('.interval_inp').html(start+'-'+end);
}
function changeli(obj){
	$(obj).find('span').addClass('hov_lisp').parent().siblings().find('span').removeClass('hov_lisp');
	var index = $(obj).index();
	$('.l_box .list_box').eq(index).show().siblings().hide();
}
//分页
$(window).scroll(function() { 
	var scrollTop = $(this).scrollTop();
	var scrollHeight = $(document).height();
	var windowHeight = $(this).height();
	var pages = Number($('.pages').val());
	if(scrollTop + windowHeight == scrollHeight){
		page = pages + 1;
		$('.pages').val(page);
		getList(page);
	}
	
})

	

