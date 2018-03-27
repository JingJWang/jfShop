//显示三种以上商品，多余隐藏
$(function(){
	$('.shop').each(function(){
		var index = $(this).index();
		if(index > 2){
			$(this).css('display','none');
			$('.loadmore').show();
		}else{
			$(this).css('display','block');
		}
	})
})
//购买商品超过三种  显示下拉箭头
function showmore(obj){
	$('.shop').css('display','block');
	$(obj).hide();
}
//当前页面加载---总计
function setAll(){
	var money=''
	$('.shop .shop_m').each(function(){
		money=Number(money)+Number($(this).children('.sums').val());
	});
	$('.god_jd').html(money);
}
// 倒计时
//显示倒计时
var timess = '';	//剩余时间
var runtimes = 0;
var timestamp1 = $('.time_s').val();		//时间基准-秒-时间戳
var timestamp2 = (new Date()).valueOf().toString();	//当前时间戳
	timestamp2 = timestamp2.substr(0,10);		//只取10位，到秒
var tenday = 86400*10;		//10天
var counttime = parseInt(timestamp1) + parseInt(tenday);	//自动收货截止时间
if(counttime >= timestamp2){
	timess = parseInt(counttime) - parseInt(timestamp2);
}else{
	timess = 0;
}
function GetRTime(){
    var nMS = timess*1000 - runtimes*1000;
    if (nMS >= 0){
        var nD=Math.floor(nMS/(1000*60*60*24))%24;
        var nH=Math.floor(nMS/(1000*60*60))%24;
        var nM=Math.floor(nMS/(1000*60)) % 60;
        var nS=Math.floor(nMS/1000) % 60;
        $('#RemainD').html(nD);
        $('#RemainH').html(nH);
        $('#RemainM').html(nM);
        $('#RemainS').html(nS);
        runtimes++;
        if(nD == 0){
            //天数0 隐藏天数
            $('#hideD').hide();
            if(nH == 0){
                //数0 隐藏小时
               	$('#hideH').hide();
                if(nM == 0){
                	//分钟数隐藏
                	$('#hideM').hide();
                    if(nS == 0){
                    	alert("倒计时完毕");
                    }
                }
            }
        }else if(nD > 0 && nH > 0 && nM >= 0 && nS >= 0){
        	$('#hideM,#hideS').hide();
        }else{			// if(nD > 0 && nH >= 0 && nM >= 0 && nS >= 0)
        	$('#hideH,#hideM,#hideS').hide();
        }
        setTimeout("GetRTime()",1000);
    }
}