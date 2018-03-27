var fall='30011';
var trues='30010';
function getList(page){
	//获取搜索参数的值 
	var name=$('.searInp').val();
	var con='';
	var page= $('.pages').val();
	var u='getList';
	var d='name='+name+'&page='+page;
	var f=function(res){
		var response=eval(res);
		if(response['status'] == trues){
			if(response['data']['result']!=''){
				$.each(response['data']['result'],function(k,v){
					con += '<li class="good">'
							+'<span class="g_name">'+v['shopname']+'</span>'//+','+v['shop_price']/100+','+v['shop_integral']/100+
							+'<span class="g_btn" onclick="showShad('+v['shopid']+');getPrice('+v['shopid']+');">发布</span>'
						+'</li>';
					$('.shopid').val(k);
				});
			}
			$('.goods').append(con);
			//分页
			var zongy = response['data']['Countpage'];
			var dangq = Number(response['data']['param']['page']);
			var lastp = 'Duang ~ 到底了';
			var jiazp = '商品加载中';
			var nohave = '没有更多商品了';
			if(zongy == 1 && dangq == 0){
				$('.tis').html(nohave);
			}else if(zongy > dangq + 1 && dangq > 0){
				$('.tis').html(jiazp);
			}else if(zongy == dangq + 1 && dangq > 0){			
				$('.tis').html(lastp);
			}
		}
	}
	conv.httpRequest(u,d,f);
}
//获取产品价格 并设置价格区间
function getPrice(id){
	var u='../../s/init/getInfo';
	var d='id='+id;
	var f=function(res){
		var response=eval(res);
		if(response['status'] == trues){
			var integral=response['data']['integral'];//金点值
			var start=0;
			var end=0;
			$('.tj_d_price').val(response['data']['integral']); //金点
			$('.tj_q_price').val(response['data']['price']);	//现金
			//价格区间在原价的上下各20%
			start=parseFloat(integral/1000*8);
			end=parseFloat(integral/1000*12);
			$('.interval_inp').html(start+'-'+end);
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
	var values=$('.moneys').val();//输入的价值
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