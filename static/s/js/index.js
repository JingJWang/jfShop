//页面开始加载----回收
function loadfun(){
	var whei = $(window).height();
	var headh = $('header').height();
	var messh = $('.co_mes').height();
	var ulh = $('.ul_tab').height();
	$('.cont').height(whei - headh - ulh);	//内容高度
	var conth = $('.cont').height();
	var chah = $('.uchange').height();
	//选择是-菜单栏高度
	$('.lichange').height(whei - headh -  ulh);
	//用户是否选择选项，判断商品列表的高度
	if($('.uchange').is(':visible')){
		$('.list_box').height(conth - chah).css({'margin-top': chah});
	}else{
		$('.list_box').height(conth);
	}
}
//页面滚动
$(window).scroll(function() { 
	var whei = $(window).height();
	var headh = $('header').height();
	var messh = $('.co_mes').height();
	var ulh = $('.ul_tab').height();
	var windtop = $(window).scrollTop();
	var documtop = $(document).scrollTop();
	if(windtop >= messh){
		$('.ul_tab').addClass('ul_fixed');
		$('.co_mes').hide();
		$('.list_box').css('overflow','auto');
		$('.cont').addClass('cont_pad');
	}
});
//商品滚动事件-分页
$('.list_box').scroll(function(){
	$('.noh_box').show();
	var scroT = $(this).scrollTop();
	var listbh = $('.showla').height();
	var listboxh = $('.list_box').height();
	var bottomh = $('.noh_box').height();
	var num = $('#page').val();
	var intea = (listbh - scroT) - (listboxh - bottomh);
	if(intea > -2 && intea < 2){
		num = Number(num) + 1;
		$('#page').val(num);
		lysavechange(num);
	}
});
//页面开始加载----商城
function shopFun(){
	loadfun();
	$('.ul_tab').addClass('ul_fixed');
	$('.cont').addClass('cont_pad').find('.list_box').css({'overflow':'auto'});
}
// //点击ul li触发
function changeli(obj){  
	//筛选点击添加data值
	if($(obj).attr('data')==0){
		$(obj).attr('data',1);
	}
	var setT = $('header').height();
	var sett = $('.co_mes').height();
	var whei = $(window).height();
	var ulh = $('.ul_tab').height();
	$('.lichange').height(whei - setT - ulh);
	var num = $(obj).index();
	//修改li的小箭头
	if($(obj).children().length != 0){
		$(obj).addClass('lt_hover').find('b').attr('class','totop');
		$(obj).siblings().removeClass('lt_hover').find('b').attr('class','tobot');
	}else{
		$(obj).addClass('lt_hover').siblings().removeClass('lt_hover').find('b').attr('class','tobot');
	}
	//设置高度--菜单选择
	$('.list_box').css({'overflow':'auto'});
	//点击时候判断该li对应的弹出显示内容-----是否显示
	if($('.lichange').find('.cshow').eq(num).is(':visible')){
		// 如果显示，则隐藏
		$('.lichange').hide().find('.cshow').eq(num).hide();
		$('.ul_tab').addClass('ul_fixed');
		$('.cont').addClass('cont_pad');	//选择框弹出，背景下面滑动，删除class定位
		// 小箭头回复原样
		if($(obj).children().length != 0){
			$(obj).removeClass('lt_hover').find('b').attr('class','tobot');
		}else{
			$(obj).removeClass('lt_hover');
		}
	}else{
		//判断是否要滑动显示
		if($('.co_mes').is(':visible')){
			//滑动显示
			$('html,body').animate({scrollTop: $(obj).parent().offset().top-setT+'px'},300,function(){
				//相对应的选择显示
				$('.lichange').show().find('.cshow').eq(num).show().siblings().hide();	//显示相对应的内容选择信息
				$('.ul_tab').addClass('ul_fixed');
				$('.cont').addClass('cont_pad');	//选择框弹出，禁止背景下面滑动，添加class定位
				$('.co_mes').hide();		//隐藏广告
			});
		}else{
			$('.lichange').show().find('.cshow').eq(num).show().siblings().hide();
			$('.ul_tab').addClass('ul_fixed');
			$('.cont').addClass('cont_pad');	//选择框弹出，禁止背景下面滑动，添加class定位
		}
	}
}
//排序  点击切换排序方式
function changpx(obj,type,param){
	$(obj).addClass('scsp_hover').siblings().removeClass('scsp_hover');
	$(obj).parent().hide();
	//判断是否点击---用来判断后面数据是否从新加载
}
//品类 左边品类切换点击事件
function changpl(obj,classe,vals){
	$(obj).addClass('hover_pleft').siblings().removeClass('hover_pleft');
	var classbig=$('#'+classe).val(vals);//品牌大类
}
//商品来源
function addchang(obj,resource,types){
	if($(obj).parent().find('span').hasClass('addcb')){
		$(obj).parent().find('span').removeClass('addcb');
	}else{
		$(obj).parent().find('span').addClass('addcb');
	}
	var source=$('#'+source).val(types);	//来源筛选
	var ly = '';
	$('.addcb').each(function(){
		ly += $(this).prev().html()+',';
	})
	$('#ly_Inp').attr('data',ly);
}
//点击隐藏弹出
function hidecs(brand,values){
	$('.lichange').hide();
	var classsmall=$('#'+brand).val(values);//品牌小类
	//保存选择品牌信息
	var pinp = '';
	$('.hov_pp_li').each(function(){
		pinp += $(this).html()+',';
	})
	//保存选择价格
	$('.jg_li').each(function(){
		if($(this).hasClass('hov_jg_li')){
			pinp = pinp + $(this).html()+',';
		}
	})
	$('#sx_Inp').attr('data',pinp);
	showjl();
}
//筛选添加class
function addC(obj,num){
	if(num == 1){
		if($(obj).hasClass('hov_pp_li')){
			$(obj).removeClass('hov_pp_li');
		}else{
			$(obj).addClass('hov_pp_li');
		}
	}else{
		$(obj).addClass('hov_jg_li').siblings().removeClass('hov_jg_li');
	}
}
//让页面滑动到顶部
function stylescrotop(){
	var setT = $('header').height();
	var ulh = $('.ul_tab').height();
	$('#page').val(0);
	$(".list_box").animate({scrollTop:$(".showla").offset().top - (setT+ulh) + 'px'},100);
}
//用户已经选择  点击x关闭
function hidechang(obj){
	var contHei = $('.list_box').height() + $('.uchange').height();
	//当前选择项是最后一个，隐藏用户选择记录的部分
	//并修改下面放商品的高
	if($(obj).parent().siblings().length == 0){
		$('.uchange').hide();
		$('.list_box').css({'margin-top': 0,'height':contHei});
	}else{
		$(obj).parent().remove();
	}
}
//显示用户记录
function showjl(){
	if($('#ly_Inp').attr('data') != '' || $('#sx_Inp').attr('data') != '' ){
		$('.uchange').show();
		var conth = $('.cont').height();
		var uchangeh = $('.uchange').height();
		uchangeh = parseInt(uchangeh);
		//商品容器的css
		$('.list_box').css({'margin-top': uchangeh,'height':conth-uchangeh});
		//拼接所有选择信息
		var arr = '';
		arr = $('#ly_Inp').attr('data') + $('#sx_Inp').attr('data');
		var arr_a = arr.split(',');
		var cont = '';		//存放所有用户选择信息
		for(var i = 0; i < arr_a.length; i++){
			if(arr_a[i] != ''){
				cont += '<div class="uc_sp">'
					+'<h1 class="uc_spCont">'+arr_a[i]+'</h1>'
					+'<b class="uc_close" onclick="hidechang(this);delChange(this);lysavechange(0);">×</b>'
				+'</div>';
			}
		}
		$('.uchange').html(cont);
	}else{
		$('.uchange').hide();
	}
}
//删除选择记录-比较相同内容
function delChange(obj){
	var choval = $(obj).prev().html();
	//删除样式
	$('.addcb').each(function(){
		if(choval == $(this).prev().html()){
			$(this).removeClass('addcb');
		}
	});
	$('.hov_pp_li').each(function(){
		if(choval == $(this).html()){
			$(this).removeClass('hov_pp_li');
		}
	});
	$('.hov_jg_li').each(function(){
		if(choval == $(this).html()){
			$(this).removeClass('hov_jg_li');
		}
	});
	var laiy = $('#ly_Inp').attr('data');
	var lei = $('#sx_Inp').attr('data');
	if(laiy.indexOf(choval) > -1){
		laiy = laiy.split(choval+',').join("");
		$('#ly_Inp').attr('data',laiy);
	}else if(lei.indexOf(choval) > -1){
		lei = lei.split(choval+',').join("");
		$('#sx_Inp').attr('data',lei);
	}
	
}
