function loadfun(){
	var whei = $(window).height();
	var headh = $('header').height();
	var ulTabh = $('.ul_tab').height();
	$('.lichange').height(whei - headh - ulTabh);
	$('.showL').height(whei - headh - ulTabh - 2);
	var showlh = $('.showL').height();
	//用户是否选择选项，判断商品列表的高度
	if($('.uchange').is(':visible')){
		$('.showList').height(showlh -40);
	}else{
		$('.showList').height(showlh);
	}
}
//页面滚动ul固定在顶部
 $(window).scroll(function(){
 	var showlah = $('.showla').height();

 	if($(window).scrollTop() >= 150){
 		$('.showList').css('overflow','auto');
 	}else{
 		$('.showList').css('overflow','hidden');
 	}
 })
 //
function changeli(obj){
	//菜单栏到顶部
	var setT = $('header').height();	
	$('.banner,.explain').hide();
	$('.ul_tab').addClass('clickul');
	//$('html,body').animate({scrollTop: $(obj).parent().offset().top-setT+'px'},300);
	//相对应的选择显示
	var num = $(obj).index();
	$('.lichange').show().find('.cshow').eq(num).show().siblings().hide();
	//修改li的小箭头
	if($(obj).children().length != 0){
		$(obj).addClass('lt_hover').find('b').attr('class','totop');
		$(obj).siblings().removeClass('lt_hover').find('b').attr('class','tobot');
	}else{
		$(obj).addClass('lt_hover').siblings().removeClass('lt_hover').find('b').attr('class','tobot');
	}
	//遮罩层出来页面禁止滚动
	//$('body').css("overflow","hidden")
}
//排序  点击切换排序方式
function changpx(obj){
	$(obj).addClass('scsp_hover').siblings().removeClass('scsp_hover');
	$(obj).parent().hide();
}
//品类 左边品类切换点击事件
function changpl(obj){
	$(obj).addClass('hover_pleft').siblings().removeClass('hover_pleft');
}
//商品来源
function addchang(obj){
	if($(obj).find('.lyb').attr('class') == 'lyb'){
		$(obj).find('.lyb').addClass('addcb');
	}else{
		$(obj).find('.lyb').removeClass('addcb');
	}
}
//商品来源-保存用户选择信息
function lysavechange(obj){
	$('.addcb').each(function(k,v){
		console.log($(v).prev().html());

	})
}

















