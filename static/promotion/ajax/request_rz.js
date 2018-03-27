var fall='30011';
var trues='30010';
var zonghe=conv.getUrlParam('zonghe');
function getList(){
	if(zonghe==''){
		alert("获取参数有误");
		return false;
	}
	var yrzcon='';//已认证
	var wrzcon='';//已认证
	var nohave = '<span class="noh_mess">暂无数据</span>';
	var u='getInviNum';
	var d='zonghe='+zonghe;
	var f=function(res){
		var response=eval(res);
		if(response['state'] == trues){
			if(response['data']['userInfo']!=''){
				$.each(response['data']['userInfo'],function(k,v){
					var con = '<div class="smess" data="'+v['status']+'">'
								+'<p class="sm_p">用户账号 : <span class="sm_span">'+v['username'].substr(0,3)+"****"+v['username'].substr(7)+'</span></p>'
								+'<p class="sm_p">日期 : <span class="sm_s">'+conv.yearTime(v['registime'])+'</span></p>'
					if(v['status'] == 1){
						yrzcon += con +'<span class="pos_rz bluer">已认证</span>'+'</div>';
					}else {
						wrzcon += con +'<span class="pos_rz redr">提醒他</span>'+'</div>';
					}
				});
			}
			$('.yrz_box').show();
			if(yrzcon == ''){
				$('.yrz_box').html(nohave)
			}else{
				$('.yrz_box').html(yrzcon);
			}
			if(wrzcon == ''){
				$('.wrz_box').html(nohave)
			}else{
				$('.wrz_box').html(wrzcon);
			}
		}
	}
	conv.httpRequest(u,d,f);
}
//style
function changeli(obj){
	var num = $(obj).index();
	$(obj).find('a').addClass('tabl_a_hover').parent().siblings().find('a').removeClass('tabl_a_hover');
	$('.rebox').eq(num).show().siblings().hide();
}