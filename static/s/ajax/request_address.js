var fall='30011';
var trues='30010';
//获取当前页面url后面的方法及参数名
var state=getUrlParae();
//选择默认地址时 获取参数 并返回确认订单列表页面
function getUrlParae(){
	var dd='';
	var param=window.location.search;//获取属性的内容
	dd=param.substring(7);
	return dd;
}
//修改地址后 获取修改地址当前的state 参数后面的的值
var pare=editUrlDef();
function editUrlDef(){
	var steta='';
	var indexs='';
	var result='';
	var param=window.location.search;//获取属性的内容
	steate=param.indexOf('state');
	indexs=steate+6;
	result=param.substring(indexs);
	return result;
}
//地址管理页面的操作 1 假删 2 修改 3 点击为默认地址
function delAddress(id,flag){
	if(flag=='del'){
		var u='updateAddress';
		var d='id='+id+'&sign='+flag+'&state='+state;
		if(confirm("你确定要删除该地址么")){
			var f=function(res){
				var response=eval(res);
				if(response['status'] == trues){
					window.location.reload();
		    	}else{
		    		alert(response['msg']);
		    		$('.surebtn').attr("onclick","delAddress("+id+","+flag+")");
		    		return false;	
		       	}
			}
		}
	}else if(flag=='edit'){
		var data=$('#editAddress').serialize();
		var u='updateAddress';
		var d=data;
		var f=function(res){
			var response=eval(res);
			if(response['status'] == trues){
				window.location.assign(response['url']+"?state="+pare);
	    	}else{
	    		alert(response['msg']);
	    		$('.surebtn').attr("onclick","delAddress("+id+","+flag+")");
	    		return false;
	       	}
		}
	}else if(flag=='default'){
		var u='updateAddress';
		var d='id='+id+'&sign='+flag;
		var f=function(res){
			var response=eval(res);
			if(response['status'] == trues){
				window.location.assign(state);
	    		
	    	}else{
	    		alert(response['msg']);
	    		$('.surebtn').attr("onclick","delAddress("+id+","+flag+")");
	    		return false;
	       	}
		}
	}
	conv.httpRequest(u,d,f);
}
//验证中文名字
function checkName(obj){
	var name=$(obj).val();
	var reg=/^[\u4E00-\u9FA5]{2,4}$/;
	if(name!='' && !reg.test(name)){
		$(obj).val("").focus();
		alert('请正确填写你的中文名字');
	}
}
//验证手机号
function testMoblie(obj){
	var myreg=/^[1][3,4,5,7,8][0-9]{9}$/;
	if(!myreg.test($(obj).val()) && $(obj).val() != ''){
		alert('输入正确的手机号！');
		$(obj).val('');
	}
}
//检查要添加的地址的数据合法性
function checkAddressInsert(){
	var mes_name=$('.mes_name').val();
	var mes_phone=$('.mes_phone').val();
	var mes_addr=$('.mes_addr').val();
	var mes_detail=$('.mes_detail').val();
	if(mes_name=="" || mes_phone=="" || mes_addr=="" || mes_detail==""){
		alert("以上信息不能为空");
		return false;
	}else{
		return true;
	}
}
//保存新添加的地址
function addAddress(state){
	checkAddressInsert();
	var data=$('#addAddress').serialize();
	var d=data;
	var u='addaddres';
	var f=function(res){
		var response=eval(res);
		if(response['status'] == trues){
			if(state !=''){
    			window.location.assign(response['url']+'?state='+state);
    		}else{
    			window.location.assign(response['url']);
    		}
    	}else{
    		alert(response['msg']);
    		$('.sureBtn').attr("onclick","addAddress("+state+")");
    		return false;
       	}
	}
	conv.httpRequest(u,d,f);
}