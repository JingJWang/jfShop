var fall='30011';
var trues='30010';
//计算确认订单页面的合计 金点/钱  
$(function () {
	var prices='';
	var integrals='';
	var money1=0;
	var money2=0;
	var counts='';
	$('.goods .goodmess').each(function(){
		var num=Number($(this).children().find('.shop_num').html());
		if($(this).find('.hid_sign').val()== 2){//钱
			price=Number($(this).children().find('.sPrice').html());
			money1 = Math.round(parseFloat(money1)*1000+parseFloat(price)*1000*num)/1000;
		}else if($(this).find('.hid_sign').val()== 1){//金点
			integrals=Number($(this).children().find('.sPrice').html());
			money2 =Math.round(parseFloat(money2)*1000+parseFloat(integrals)*1000*num)/1000;
		}
	});
	if(money1 !='' && money2 == ''){
		counts =money1+'元';
	}
	if(money2!='' && money1==''){
		counts =money2+'金点';
	}
	if(money1 !='' && money2!=''){
		counts =money1+'元'+'+'+money2+'金点';
	}
	$('.duoshangpin').html(counts);
  
})
//验证中文名字
function checkName(obj){
	var name=$(obj).val();
	var reg=/^[\u4E00-\u9FA5]{2,4}$/;
	if(name!='' && !reg.test(name)){
		$(obj).val("").focus();
		alert('请正确填写你的中文名字');
	}
}
//验证手机号
function testMoblie(obj){
	var myreg=/^[1][3,4,5,7,8][0-9]{9}$/;
	if(!myreg.test($(obj).val()) && $(obj).val() != ''){
		alert('输入正确的手机号！');
		$(obj).val('');
	}else{
		return '&phone='+$(obj).val();
	}
}
//效验参数
function checkSubOrder(state){
	var checks='';
	if(state ==1){
		var reg = /^\+?[1-9][0-9]*$/;
		var hid_id=$('.hid_id').val();
		var hid_sign=$('.hid_sign').val();
		var hid_inpNum=$('.hid_inpNum').val();
		if(!reg.test(hid_id) || !reg.test(hid_sign) || !reg.test(hid_inpNum)){
			alert('禁止非法参数');
			return false;
		}
		checks='check=['+hid_id+'/'+hid_sign+'/'+hid_inpNum+'/0'+']'+'&';
	}else if(state==2){
		var dange='';
		var hid_id = '';
		var hid_sign = '';
		var hid_inpNum = '';
		var hid_packid = '';
		var dange='';
		$('.goods .goodmess').each(function(){
			var reg = /^\+?[1-9][0-9]*$/;  
			var hid_id=$(this).children('.hid_id').val();
			var hid_sign=$(this).children('.hid_sign').val();
			var hid_inpNum=$(this).children('.hid_inpNum').val();
			var hid_packid=$(this).children('.hid_packid').val();
			if(!reg.test(hid_id) || !reg.test(hid_sign) || !reg.test(hid_inpNum) || !reg.test(hid_packid)){
				alert('禁止非法参数');
				return false;
			}
			dange=dange+'['+hid_id+'/'+hid_sign+'/'+hid_inpNum+'/'+hid_packid+'];';
		})
		dange = dange.substring(0, dange.lastIndexOf(';'));
		checks +='check='+dange+'&';
	}
	return checks;
}
//提交订单
function submitOrder(state){
	var name=$('.nameInp').val();
	var phone=$('.phoneInp').val();
	if(name=='' || phone==''){
		alert('信息不能为空');
		return false;
	}
	var d=checkSubOrder(state)+'name='+name+'&phone='+phone+'&state='+state;
	var u='submitOrder';
	var f=function(res){
		$('.f_r').remove("onclick");
		var response=eval(res);
			if(response['status'] == trues){
    		window.location.assign(response['url']);
    	}else{
    		alert(response['msg']);
    		$('.f_r').attr("onclick","submitOrder()");
    		return false;
    	}
	}
	conv.httpRequest(u,d,f);
}
//填写物流 确认提交
function yesTran(){
	var kdname=$('.kdnameInp').val();
	var kdNum=$('.kdNumInp').val();
	var orderNum=$('.orderNum').val();
	if(kdname=='' || kdNum=='' || orderNum==""){
		alert("信息不能为空");
		return false;
	}
	var u='upOrderTransport';
	var d='orderNum='+orderNum+'&kdNum='+kdNum+'&kdname='+kdname;
	var f=function(res){
		$('.f_r').remove("onclick");
		var response=eval(res);
		if(response['status'] == trues){
			alert('添加成功');
			window.location.reload();//页面刷新
    	}else{
    		alert(response['msg']);
    		$('.f_r').attr("onclick","submitOrder()");
    		return false;
    	}
	}
	conv.httpRequest(u,d,f);
}
//取消订单
function cancleOrder(){
	var orderId=$('.orderNum').val();
	if(orderId==''){
		alert("非法查收请求");
		return false;
	}
	var u='cancelOrder';
	var d='orderId='+orderId;
	var f=function(res){
		var response=eval(res);
		if(response['status'] == tures){
			window.location.reload();//页面刷新
    	}else{
    		alert(response['msg']);
    		return false;
    	}
	}
	conv.httpRequest(u,d,f);
}
//获取物流记录信息
function expressRecord(){
	var content='';
	var kdnumber=$('.kdNumInp').val();
	if(kdnumber==''){
		alert("获取参数有误");
		return false;
	}
	var u='expressRecord';
	var d='kdnumber='+kdnumber;
	var f=function(res){
		var response=eval(res);
		if(response['status'] == trues){
    		for(var i=0;i<response['data'].length;i++){
    			content +='<li>'
        			+'<p class="time_l">'
        				+'<span class="tl_day">'+conv.nowTime(response['data'][i][0])+'</span>'
        				+'<span class="tl_time">'+conv.minusTime(response['data'][i][0])+'</span>'
        			+'</p>'
    			+'<p class="center_line"><span></span></p>'
    			+'<p class="mess_r">'+response['data'][i][1]+'</p>'
        			+'</li>';
    		}
    		$('.wl_li').html(content);	
    	}
	}
	conv.httpRequest(u,d,f);
}
//显示三种以上商品，多余隐藏
$(function(){
	$('.goods').each(function(){
		var index = $(this).index();
		if(index > 1){
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
//返回
function backFun(){
	$('.shadow').height($(window).height()).css({'overflow':'hidden'});
	$('body').css({'overflow':'hidden'});
	$('.shadow').slideDown(50,function(){
		$('.last').show();
	});
}
//回收车弹框删除
function remFun(num){
	if(num == 1){
		$('.shadow,.last').hide();
		history.go(-1);
	}
	$('.shadow,.last').hide();
	$('body').css({'overflow':'auto'});
}