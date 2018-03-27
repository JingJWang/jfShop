var fall='30011';
var trues='30010';
//获取分类大类
function getType(){
	var content='';//分类显示的内容
	var u='getTypeName';
	var d='';
	var f=function(res){
		var response=eval(res);
		if(response['status'] == trues){
    		 $.each(response['data'],function(k,v){
    			 content+='<a href="javascript:;" '
    			 +'class="lchild" data="'+v['typeid']+'" onclick="changpl(this,\'classbig\','+v['typeid']+');'
    			 +"getSmallType("+v['typeid']+');lysavechange(0)">'+v['typename']+'</a>';
    		 })
    		 getSmallType(1);
    		 $('.clas_left').html(content);
    		 $('.lchild').eq(0).addClass('hover_pleft');
    		 var typeid=$('.hover_pleft').attr('data');
    		 $('#classbig').val(typeid);
    	}
	}
	conv.httpRequest(u,d,f);
}
//获取分类小类
function getSmallType(pid){
	var content='';//分类显示的内容
	var u='getTypeSmallName';
	var d='id='+pid;
	var f=function(res){
		var response=eval(res);
		if(response['status'] == trues){
			$.each(response['data'],function(k,v){
    			 content+='<a href="javascript:;" '
    			 +'class="rchild" data="'+v['typeid']+'" onclick="hidecs(\'classsmall\','+v['typeid']+');'+
    			 'getSTClass('+v['typeid']+');lysavechange(0);stylescrotop();"><img class="cl_r_img" src="'+v['img']+'"><span class="cl_r_sp">'+v['typename']+'</span></a>';
    		 })
    		 
    		 $('.clas_right').html(content);
    		 $('.clas_right').append('');
    		 var typeid=$('.clas_right').children().eq(0).attr('data');
    		 if(typeid!=''){
    			 getSTClass(typeid)
    		 }
    	}
	}
	conv.httpRequest(u,d,f);
}
// 获取小类的品牌
function getSTClass(typeid){
	var content='';//分类显示的内容
	var u='getGoodClass';
	var d='id='+typeid;
	var f=function(res){
		var response=eval(res);
		if(response['status'] == trues){
			$.each(response['data'],function(k,v){
    			 content+='<li class="pp_li" data="'+v['brandid']+'" onclick="addC(this,1);lysavechange(0)"stclass('+v['brandid']+')>'+v['brandname']+'</li>';
    		 })
    		 $('.pp_ul').html(content);
    	}
	}
	conv.httpRequest(u,d,f);
}
//商品来源-保存用户选择信息
function lysavechange(num){
    $('.nohave').hide();
    $('.nohave_p').html('');
    if(num == 0){
        $('.showla').html('');
        $('#page').val(num);
    }
    var pagenum = $('#page').val();
    var content='';
    var source='';    //来源
    var brand='';     //品牌
    //来源
    $('.addcb').each(function(k,v){
        source += $(v).prev().attr('data')+';'
    });
    //-----分类-品牌
    //获取筛选中的品牌
    $('.pp_ul .hov_pp_li').each(function(k,v){
        brand +=$(this).attr('data')+';';
    });
    //获取筛选的价格区间
    var price=$('.onecs .scsp_hover').attr('data');
    $('#price').val(price);
    //获取筛选的价格
    var pri='';
    var str=$('.jg_ul .hov_jg_li').html();
    if($('.ul_tab .li_tab').eq(3).attr('data') == 1){
        if($('.hov_jg_li').length == 0){
            pri='';
        }else{
            pri=str.split("金")[0];
        }
    }else{
        pri='';
    }
    //来源赋值
    var sous='';
    var datas = $('#search').serialize();
    var u='ajaxlists';
    var d='source='+source+'&pri='+pri+'&brand='+brand+'&'+datas;
    var f=function(res){
        var response=eval(res);
        //判断返回的结果
        if(response['status'] == trues){
            //返回结果不为''
            content = '';
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
                    content +='<a href="shopinfo?id='+v['shop_id']+'" class="slista">'+
                            '<div class="spimg">'+
                            '<img class="shop_img" src="'+v['shop_img']+'">'+  
                            '<span class="shop_by">包邮</span></div>'+
                            '<div class="shop_mesg">'+
                            '<span class="shopName c_shopn">'+v['shop_name']+'</span>'+
                            '<span class="shopprice c_shopp"><b class="jdColor">'+v['shop_integral']/100+'</b>金点 / <b class="pirceM">'+v['shop_price']/100+'</b>元</span>'+
                            '<p class="shopdh_change">'+sous+'兑换价：<b class="sjf">'+v['shop_source_info']['credit']+'</b>积分 </p>'+
							'<p class="shopdh_change">市场价: <b class="jspric">'+v['shop_source_info']['price']/100+'</b>元'+
							'</p></div></a>';
                })
            }else{
               content = '';
            }
            //判断当前页数
            if(response['data']['Countpage'] == 0 && pagenum == 0){
                $('.nohave_p').html('目前没有相关商品~');
                $(window).unbind('scroll');    //解禁window的scroll事件
            }else if(pagenum  > 0 && response['data']['Countpage'] == 0){
                $('.nohave_p').html('到底了~');
                $(window).unbind('scroll');    //解禁window的scroll事件
            }else if(response['data']['Countpage'] > 1 && response['data']['Countpage'] > num+1){
                $('.nohave_p').html('努力加载中~');
            }else if(response['data']['Countpage'] == 1){
                $('.nohave_p').html('没有更多商品了~');
                $(window).unbind('scroll');    //解禁window的scroll事件
            }
            $('.showla').append(content);
        }
    }
    conv.httpRequest(u,d,f);
}