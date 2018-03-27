function show(){
	$('.shadow,.p_last').show();
}
function hideshad(){
	$('.shadow,.p_last').hide();
}
var oBtn = $(".btnShare");
var oShadow = $(".shadow");
var ua = window.navigator.userAgent.toLowerCase();
var iswx = (ua.match(/MicroMessenger/i) == 'micromessenger')?1:0;
oBtn.on("click",function(){
    if (iswx==1) {
        oShadow.show();
    }else{
        $(".fuzhi").show();
    }
})
oShadow.on("click",function(){
     $(this).hide();
})
$(".fuzhi").on("click",function(){
     $(this).hide();
})