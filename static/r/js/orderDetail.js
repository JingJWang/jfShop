function shadowshow(num){
	if(num == 1){
		$('.kefu_box').show();
	}else if(num ==2){
		$('.can_list').show();
	}else{
		$('.wul_box').show();
	}
	$('.shadow').show();
	$('body').css('overflow','hidden');
}
function hideshad(num){
	if(num == 1){
		$('.kefu_box').hide();
	}else if(num ==2){
		$('.can_list').hide();
	}else if(num ==3){
		$('.wul_box').hide();
	}else{
		$('.shad').hide();	//点击遮罩层全部隐藏
	}
	$('.shadow').hide();
	$('body').css('overflow','auto');
}
// 正则
function bl_inp(obj,num){
	var te = /^[\u4E00-\u9FA5]{2,6}$/;		//快递公司
	var tenum = /^[0-9a-zA-Z]{10,14}$/;		//快递单号
	if(num == 1){
		if(!te.test($(obj).val()) && $(obj).val() != ''){
			alert('请输入正确的快递公司！');
			$(obj).val('');
		}else{
			return true;
		}
	}else{
		if(!tenum.test($(obj).val()) && $(obj).val() != ''){
			alert('请输入正确的快递单号！');
			$(obj).val('');
		}else{
			return true;
		}
	}
}
//显示三种以上商品，多余隐藏
$(function(){
	$('.goods').each(function(){
		var index = $(this).index();
		if(index > 2){
			$(this).css('display','none');
			$('.loadmore').show();
		}else{
			$(this).css('display','block');
		}
	})
})
//购买商品超过三种  显示下拉箭头
function showmore(obj){
	$('.goods').css('display','block');
	$(obj).hide();
}