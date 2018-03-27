function liChange(obj){
	$(obj).addClass('hov_li').parent().siblings().find('.li_sp').removeClass('hov_li');
	var index = $(obj).parent().index();
	$('.c_mess').eq(index).show().siblings().hide();
}
//数量加减
function numFun(obj,num){
	var inpNum = $(obj).parent().find('.shownum').val();
	if(num == 1){
		inpNum++;
	}else{
		if(inpNum == 1){
			inpNum == 1;
		}else{
			inpNum--;
		}
	}
	$(obj).parent().find('input').val(inpNum);
	$(obj).parent().find('.shownum').attr('data',inpNum);
	//hejiPrice();
}
//判断显示购物车里的数量
function gocartFun(){
	if($('.numberSp').html() == ''){
		$('.numberSp').hide();
	}
}
//立刻购买
function changeInp(){
	var nums = $('.shownum').attr('data');
	var shopIds=$('.shopIds').val();
	var index=shopIds.lastIndexOf('=');
	shopIds = shopIds.substring(0 ,index+1);
	shopIds=shopIds+nums;
	$('.shopIds').val(shopIds);
}