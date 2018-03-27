var fall='30011';
var trues='30010';
//效验获取参数
function chekData(obj,type,num){
	var pri='';//金点区间
	var source='';//选择来源
	var classes='';//类别
	var kind='';//商品展示 好货 新品 热销
	switch(type){
		case 'pri':
			pri=$(obj).html().split("金")[0];
			break;
		case 'type':
			type=$(obj).attr('data');
		case 'source':
			type=$(obj).attr('data');
			break;
		case 'kind':
			kind=$(obj).attr('data');
			break;
		defalt:;break;
	}
	return 'pri='+pri+'&type='+type+'&source='+source+'&kind='+kind+'&page='+num;
}
function formtijiao(obj){
	$(obj).parent().find('input').val($(obj).attr('data'));
	$('.formpri').submit();
}
//商品来源-保存用户选择信息
function check(obj,type,num){
	if(num == 0){
        $('.showla').html('');
        $('.resLen').val(num);
        $('.nohave').show();
    }
    var u='ajaxlists';
    var d=chekData(obj,type,num);
    var f=function(res){
        var response=eval(res);
        //判断返回的结果
        if(response['status'] == fall){       //返回结果
            $('.showla').html('没有查到相应的商品!');
        }else{
            content = '';
            sous='';
            if(response['data']['result'] != ''){
                $.each(response['data']['result'],function(k,v){
                    //判断来源
                	switch(v['shop_source']){
                     case 'ZS':
                         sous='招商银行';
                            break;
                     case 'JT':
                         sous='交通银行';
                            break;
                     case 'YD':
                         sous='中国移动';
                            break;
                     default:'';
                        break;
                  }
            	content +='<a href="../../s/init/shopinfo?id='+v['shop_id']+'" class="slista">'
        			+'<div class="spimg">'
        				+'<img class="shop_img" src="'+v['shop_img']+'">'
        				+'<span class="shop_by">包邮</span>'
        			+'</div>'
        			+'<div class="shop_mesg">'
        				+'<span class="shopName">'+v['shop_name']+'</span>'
        				+'<span class="shopprice"><b class="jdColor">'+v['shop_integral']/100+'</b>金点 / <b class="pirceM">'+v['shop_price']/100+'</b>元</span>'
        				+'<p class="shopdh">'+sous+'兑换价：<b class="sjf">'+v['shop_source_info']['credit']+'</b>积分</p>'
        				+'<p class="shopdh">市场价：<b class="sjf">'+v['shop_source_info']['price']/100+'</b>元	</p>'
        			+'</div>'
    			+'</a>';
                })
            }else{
               content = '<p class="nohave">没有查到相应的商品</p>';
            }
	        $(obj).addClass('hover_mli').parent().siblings().find('span').removeClass('hover_mli');
            $('.showla').html(content);
        }
    }
    conv.httpRequest(u,d,f);
}