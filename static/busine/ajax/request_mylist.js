var fall='30011';
var trues='30010';
function getList(state){
	//获取搜索参数的值 
	var con='';//内容显示
	var mon='';//价格/数量显示
	var time='';//购买时间
	var page=0;
	if(state=='buy'){
		var u='buyOrder';
	}
	
	var d='state='+state+'&page='+page;
	var f=function(res){
		var response=eval(res);
		if(response['status'] == trues){
			if(response['data']['goodslist']!=''){
				
			}
			if(response['data']['order']!=''){
				$.each(response['data']['order'],function(k,v){
					if(v['price']==0){
						mon='<b class="l_price">'+v['integral']/100+'</b><b class="l_pay">金点</b> / <b class="l_number">'+v['num']+'</b>';
					}
					if(v['order_updatetime']=='0'){
						time=conv.housTime(v['order_jointime']);
					}else{
						time=conv.housTime(v['order_updatetime']);
					}
					con += '<li class="listLi">'
						+'<span>&nbsp;&nbsp;订单编号:'+v['number']+'</span>'
							+'<div class="li_tit">'
								+'<span class="lit">商品名称</span>'
								+'<span class="lit">价格 / 数量</span>'
								+'<span class="lit">成交时间</span>'
							+'</div>'
							+'<div class="li_tit">'
								+'<span class="litwo">'+v['name']+'</span>'
								+'<span class="litwo teali">'+mon+'</span>'
								+'<span class="litwo">'+time+'</span>'
							+'</div>'
						+'</li>';
					
				});
			}
			$('.buyorder').html(con);
		}
	}
	conv.httpRequest(u,d,f);
}
//发布商品
function release(){
	var page=0;
	//获取商品的id
	var shopid=$('.shopid').val();
	var method=$('.check_rad').attr('data');//选择交易方式
	var values=$('.price').val();//输入的价值
	var num=$('.num').val();
	if(shopid=='' || method=='' || values=='' || num==''){
		alert("参数不能为空");
		return false
	}
	if(shopid<0 || method<0 || values<0 || num<0){
		alert("参数错误");
		return false;
	}
	var u='addBusine';
	var d='shopid='+shopid+'&method='+method+'&values='+values+'&num='+num+'&page='+page;
	var f=function(res){
		var response=eval(res);
		if(response['status'] == trues){
			alert("发布成功");
			window.location.assign(response['url']);
		}else{
			alert(response['msg']);
			window.location.reload();
		}
	}
	conv.httpRequest(u,d,f);
}