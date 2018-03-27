var fall='30011';
var trues='30010';
var zonghe=conv.getUrlParam('zonghe');
//console.log(zonghe);
//验证手机号
function testMoblie(obj){
	var myreg=/^[1][3,4,5,7,8][0-9]{9}$/;
	if(!myreg.test($(obj).val()) && $(obj).val() != ''){
		alert('输入正确的手机号！');
		$(obj).val('');
	}else{
		return $(obj).val();
	}
}
function share(){
	var mobile=$('.mob').val();
	var u='shareInsUserTask';
	var d='zonghe='+zonghe+'&username='+mobile;
	var f=function(res){
		var response=eval(res);
		if(response['status'] == trues){
			alert("领取成功");
			$('.wlq').hide();
			$('.ylq').show();
			$('.mobile').html(response['data']);
			$('.btngo').attr('href',response['url']+'?zonghe='+zonghe);
		}else{
			alert(response['msg']);
			$('.wlq').show();
			$('.ylq').hide();
		}
	}
	conv.httpRequest(u,d,f);
}
function URLencode(str) {
    var ret = "";
    var strSpecial = "!\"#$%&'()*+,/:;<=>?[]^`{|}~%";
    var tt= "";
    for(var i=0;i<str.length;i++) {
            var chr = str.charAt(i);
            var c = str2asc(chr);//这里用到了VBscript
            tt += chr+":"+c+"n";
            if(parseInt("0x"+c) > 0x7f) {
                    ret += "%"+c.slice(0,2)+"%"+c.slice(-2);
            }else {
                    if(chr == " ") ret += "+";
                    else if(strSpecial.indexOf(chr)!=-1) ret += "%"+c.toString(16);
                    else ret += chr;
            }
    }
    return ret;
}
