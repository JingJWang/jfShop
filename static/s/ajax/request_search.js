var fall='31011';
var trues='31010'
function checkSearch(){
	var search=$('.sous').val();//分类显示的内容
	var u='cunSearch';
	var d='search='+search;
	var f=function(res){
		var response=eval(res);
		if(response['status'] == trues){
				return true;
    	}
	}
	conv.httpRequest(u,d,f);
}
//删除最近搜索
function delSearch(){
	var u='delSearch';
	var d='';
	var f=function(res){
		var response=eval(res);
		if(response['status'] == trues){
				return true;
    	}
	}
	conv.httpRequest(u,d,f);
}