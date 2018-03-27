var fall='30011';
var trues='30010';
//回收-商城 1 全部 2待支付 3待收货 4已完成 5已取消
function changClass(obj,state,page){
	if(page==0){
		$('#page').val(0);
		$('.list').html('');
	}
	$(obj).addClass('li_hover').siblings().removeClass('li_hover');
	var content='';
	var resu='';
	var olist='';
	var heji='';
	var econtent='';
	if(state==''){
		state=='all';
	}
	$('footer').show();
	var u='getOrderList';
	var d='status='+state+'&page='+page;
	var f=function(res){
		var response=eval(res);
		if(response['status'] == trues){
    		if(response['data']['result'].length>0){
    			$.each(response['data']['result'],function(k,v){
				switch(v['state']){
					case '1':
						resu='已下单 ';break;
					case '2':
						resu='待发货 ';break;
					case '3':
						resu='已发货 ';break;
					case '4':
						resu='已取消 ';break;
					case '10':
						resu='已完成 ';break;
				}
				//console.log(v['price']+'***'+v['integral']);
				if(v['price']!=0 && v['integral']!=0){
					heji=v['price']/100+'元+'+v['integral']/100+'金点';
				}else if(v['price']==0 && v['integral']!=0){
					heji=v['integral']/100+'金点';
				}else if(v['price']!=0 && v['integral']==0){
					heji=v['price']/100+'元';
				}
				if(v['res'].length>0){
					$.each(v['res'],function(ks,vs){
						var price='';
						var ste='';
						var yhmz='';
						if(vs['method']==2){
							price=vs['price']/100;
							ste='元';
						}else  if(vs['method']==1){
							price=vs['integral']/100;
							ste='金点';
						}
						if(vs['source']=="ZS"){
							yhmz='招商'
						}else if(vs['source']=="JT"){
							yhmz='交通'
						}else if(vs['source']=="YD"){
							yhmz='移动'
						}
						//olist +='<img src="'+v['res'][ks]['img']+'" class="goodimg">'
	     				olist ='<a href="orderDetail?orderId='+v['number']+'" class="gdmess">'
							+'<img src="'+vs['img']+'" class="goodimg">'
							+'<span class="shopname">'+vs['goodsname']+'</span>'
								+'<p class="shoPrice">价格：'
									+'<span class="sPrice">'+price+'</span>'
									+'<span class="spay">'+ste+'</span>'
								+'</p>'
	     						+'<p class="shoPrice">数量：<span class="god_num">'
								+vs['num']+'</span></p>'
									+'<p class="good_js">商品来源：<span class="goods_span">'
								
								+yhmz+'银行积分商城</span></p>'
								+'<p class="good_js">共计获得：<span class="goods_span">'
									+heji+'</span></p>'
								+'</p>'
							    +'</a>';
	     			})
				}
         		content +='<div class="gods"><a href="orderDetail?orderId='+v['number']+'" class="gdmess">' +olist+'</a><div class="list_sta">'+resu+'</div></div>';
         		})
    		}else{
    			content=''; //<div class="nohave_list">您目前没有相关订单</div>
    		}
    		//console.log(response['data']['result']);		//数据
    		//console.log(response['data']['nums']);
    		if(response['data']['nums'] == page && response['data']['nums'] == 1){
    			$('footer').html('');
    			$(window).unbind ('scroll');	//解禁window的scroll事件
    		}else if(response['data']['nums'] == 0 && page == 0){
    			var nolist = '<div class="nohave_list">您目前没有相关订单</div>';
    			$('footer').hide();
    			$('.list').html(nolist);
    		}else if(response['data']['nums'] == page && response['data']['nums'] > 1){
    			$(window).unbind ('scroll');	//解禁window的scroll事件
    			$('footer').html('到底了~');
    		}else{
    			$('.list').append(content);
    		}
    	}
	}
	conv.httpRequest(u,d,f);
}
//滚动条事件
$(window).on("resize scroll",function(){
	var $currentWindow = $(window);             
	var windowHeight = $currentWindow.height();//当前窗口的高度             
	var scrollTop = $currentWindow.scrollTop();//当前滚动条从上往下滚动的距离            
	var docHeight = $(document).height(); //当前文档的高度 
	var num = $('#page').val();
	var state=$('.li_hover').attr('data');
	//当 滚动条距底部的距离 + 滚动条滚动的距离 >= 文档的高度 - 窗口的高度  
	//换句话说：（滚动条滚动的距离 + 窗口的高度 = 文档的高度）  这个是基本的公式  
	if (scrollTop + windowHeight >= docHeight) { 
		 num = Number(num)+1;
		 $('#page').val(num);
		 changClass(this,state,num);
	} 
});
//点击到页面顶部
function stylescrotop(){
	var setT = $('header').height();
	var ulh = $('.ul_s').height();
	$('#page').val(0);
	$("html,body").animate({scrollTop:$(".list").offset().top - (setT+ulh) + 'px'},100);
}