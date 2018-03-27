var fall='30011';
var trues='30010';
function getLists(){
	//获取搜索参数的值 
	var name=$('.searInp').val();
	var con='';
	var page=0;
	var stat='';
	var u='getReleaseShop';
	var d='name='+name+'&page='+page;
	var f=function(res){
		var response=eval(res);
		if(response['status'] == trues){
			if(response['data']['result']!=''){
				$.each(response['data']['result'],function(k,v){
					if(v['price']==0){
						stat='<b class="b_price">'+v['integral']/100+'</b><b class="b_pay">金点</b>';
					}else{
						stat='<b class="b_price">'+v['price']/100+'</b><b class="b_pay">元</b>';
					}
					if(v['status']==-1){
						stut='<b class="b_sta" onclick="updateBusineStatus('+v['shopid']+',1);">上架</b>';
					}else{
						stut='<b class="b_sta" onclick="updateBusineStatus('+v['shopid']+',-1);">下架</b>';
					}
					con += '<li class="list_li">'
						+'<span class="li_sp sName">'+v['shopName']+'</span>'
						+'<span class="li_sp prinum">'+stat+' / <b class="b_num">'+v['num']+'</b></span>'
						+'<span class="li_sp sp_stat">'+stut+'  '
						+'<b class="b_bianj" onclick="showShad();updateBusine('+v['shopid']+');">编辑</b></span>'
					+'</li>';
				});
			}
		}else{
			con='没有结果';
		}
		$('.list').html(con);
	}
	conv.httpRequest(u,d,f);
}
//商家点击商品上架or下架
function updateBusineStatus(id,status){
	var u='updateBusineStatus';
	var d='shopid='+id+'&status='+status;
	var f=function(res){
		var response=eval(res);
		if(response['status'] == trues){
			window.location.reload();
		}else{
			alert('操作失败');
		}
	}
	conv.httpRequest(u,d,f);
}
//商家修改商品 先获取商品参数信息
function updateBusine(id){
	var method=1;
	var u='getShopOneBusine';
	var d='shopid='+id;
	var f=function(res){
		var response=eval(res);
		if(response['status'] == trues){
			if(response['data']['busine_price']==0){
				method=2;//金点
				$('.radio').eq(0).addClass('check_rad').siblings('.radio').removeClass('check_rad');
				$('.price').val(response['data']['busine_integral']/100);
			}else{
				$('.radio').eq(1).addClass('check_rad').siblings('.radio').removeClass('check_rad');
				$('.price').val(response['data']['busine_price']/100);
				$('.danw').html('元');
			}
			$('.shopid').val(response['data']['shop_id']);
			$('.num').val(response['data']['busine_num']);
		}else{
			alert("获取结果失败");
			return false;
		}
	}
	conv.httpRequest(u,d,f);
}
//修改商品
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
	var u='updateBusine';
	var d='shopid='+shopid+'&method='+method+'&values='+values+'&num='+num+'&page='+page;
	var f=function(res){
		var response=eval(res);
		if(response['status'] == trues){
			alert("修改成功");
			window.location.assign('lookReleashTem');
		}
	}
	conv.httpRequest(u,d,f);
}