/**
 * 
 */

var conv={
	getUrlParam:function (name) {
			var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)"); //构造一个含有目标参数的正则表达式对象
			var r = window.location.search.substr(1).match(reg);  //匹配目标参数
			if (r != null) return unescape(r[2]); return null; //返回参数值
	},
	httpRequest:function (u,d,f,m='post'){
		var result='';
		$.ajax({
	        url:u,
	        type:m,
	        dataType:"json",
	        data:d,
	        async: false,
	        beforeSend: function(){
	        },
	        success:function(res){	
	        	  f(res);
	        },
	        complete: function(res){ 
	       	},
	        error:function(msg){
	        	return false;
	        }
	    });
	},
	//时间转换 年月日
	yearTime:function(now){
		var now = new Date(now * 1000);
		var month = now.getMonth() + 1;
		var date = now.getDate();
		var year = now.getFullYear();
		var hour = now.getHours();
		var minute = now.getMinutes();
		return   year+"年"+month+"月"+date+"日";
	},
	//时间转换 年月日时分
	housTime:function(now){
		var now = new Date(now * 1000);
		var month = now.getMonth() + 1;
		var date = now.getDate();
		var year = now.getFullYear();
		var hour = now.getHours();
		var minute = now.getMinutes();
		return   year+"."+month+"."+date+". "+hour+":"+minute;
	},
	//时间转换 月日
	nowTime:function(now){
		var now = new Date(now * 1000);
		var month = now.getMonth() + 1;
		var date = now.getDate();
		var year = now.getYear();
		var hour = now.getHours();
		var minute = now.getMinutes();
		//return   year+"年"+(month,2)+"月"+fixZero(date,2)+"日    "+fixZero(hour,2)+":"+fixZero(minute,2)+":"+fixZero(second,2);
		return   month+"月"+date+"日"; 
	},
	//时间转换 月日
	minusTime:function(now){
		var now = new Date(now * 1000);
		var month = now.getMonth() + 1;
		var date = now.getDate();
		var hour = now.getHours();
		var minute = now.getMinutes();
		//return   year+"年"+(month,2)+"月"+fixZero(date,2)+"日    "+fixZero(hour,2)+":"+fixZero(minute,2)+":"+fixZero(second,2);
		return   hour+"点"+minute+"分"; 
	}
}
	
