var fall='30011';
var trues='30010';
//编辑全选   编辑num1   全选num2
function showfoot(obj,num){
	if(num == 1){
		$(obj).html('完成').attr('onclick','showfoot(this,2);');
		$('.account').html('删除').attr('onclick','sCartFun(2);');	//删除添加点击事件
	}else{
		$(obj).html('编辑').attr('onclick','showfoot(this,1);');
		$('.account').html('去结算').attr('onclick','sCartFun(1);');		//结算
	}
}
//用户选择是否结算
function changeradio(obj){
	var num = 0;
	var anum = 0;
	if($(obj).hasClass('rad_check')){
		$(obj).removeClass('rad_check');
	}else{
		$(obj).addClass('rad_check');
	}
	//已选
	$('.list_box .rad_check').each(function(){
		num += $(this).length;
	})
	//所有
	$('.list_box .rad').each(function(){
		anum += $(this).length;
	})
	// 显示总计
	if(num != 0){
		$('.all').show();
	}else{
		$('.all').hide();
	}
	//全选是否显示
	if(num == anum){
		$('.fir_qx .changrad').addClass('rad_check');
	}else{
		$('.fir_qx .changrad').removeClass('rad_check');
	}
	hejiPrice();
}
// 全选
function allcheck(obj){
	if($(obj).find('span').hasClass('rad_check')){
		$('.fir_qx .changrad').removeClass('rad_check');
		$('.list_box .rad_check').each(function(){
			$(this).removeClass('rad_check');
		});
		$('.all').hide();
	}else{
		$('.fir_qx .changrad').addClass('rad_check');
		$('.list_box .rad').each(function(){
			$(this).addClass('rad_check');
		});
		$('.all').show();
	}
	hejiPrice();
}
//合计总价
function hejiPrice(){
	//总价变量
	var count='';
	var money=0;
	var integral=0;
	var num=1;
	//合计总价
	var hasinp=$('.list .rad_check').parent().parent().find('.hidprice');
	$(hasinp).each(function(){
		num=Number($(this).prev().find('.shownum').val())
		if($(this).attr('data') == 2){//金钱
			money =Math.round(parseFloat(money)*1000+parseFloat($(this).val())*1000*num)/1000;
		}else if($(this).attr('data') == 1){//金点
			integral =Math.round(parseFloat(integral)*1000+parseFloat($(this).val())*1000*num)/1000;
		} 
	})
	if(money!='' && integral!=''){
		count=money+'元'+'+'+integral+'金点';
	}else if(money!='' && integral==''){
		count = money+'元';
	}else if(money=='' && integral!=''){
		count = integral+'金点';
	}
	$('.all .hidShop').html(count);
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
	$(obj).parent().find('.shownum').val(inpNum);
	//取id值
	var packid = $(obj).parent().parent().parent().prev().find('.packageid').val();
	//在input的data 存值
	$(obj).parent().parent().parent().prev().find('.packageid').attr('data',inpNum)
	$(obj).parent().find('.shownum').attr('data',inpNum);
	hejiPrice();
	var u='updateCart';
	var d='packid='+packid+'&num='+inpNum;
	var f=function(res){
		var response=eval(res);
		if(response['status'] == fall){
    		return false;
    		alert('修改数量出现异常！');
    	}else{
    		 return true;
    	}
	}
	conv.httpRequest(u,d,f);	
}