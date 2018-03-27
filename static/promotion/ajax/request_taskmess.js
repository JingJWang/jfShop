var fall='30011';
var trues='30010';
function share(id){
	var u='upUserTask';
	var d='suiji='+suiji+'&id='+id;
	var f=function(res){
		var response=eval(res);
		if(response['status'] == trues){
			
		}	
	}
	conv.httpRequest(u,d,f);
}