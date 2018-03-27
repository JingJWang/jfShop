//清除历史记录
function hideSear(){
	$('.history').slideUp();
	$('.hot').addClass('martop');	
}
function seachName(obj){
	$('.sous').val($(obj).html());
	$('#searchName').submit();
}