//style -- li点击添加效果
function hoverSty(obj){
	var num = $(obj).index();
	$(obj).find('a').addClass('hovera').parent().siblings().find('a').removeClass('hovera');
	$('.ul_box ul').eq(num).show().siblings().hide();
}
var fall='30011';
var trues='30010';
function getTaskList(){
	var fircon='';
	var twocon='';
	var threcon='';
	var fourcon='';
	var u='getList';
	var d='page=0';
	var f=function(res){
		var response=eval(res);
		if(response['status'] == trues){
			//全部任务
			console.log(response['data']['rmrw'].length);
			if(response['data']['task']){
				$.each(response['data']['task'],function(k,v){
					//获取任务循环状态
					if(v['count']){
						//获取任务是否是循环或者限时任务
						if(v['count']['type']==2){
	    					stu="(循环)";
	    				}else if(v['count']['type']==3){
	    					stu="(限时任务)";
	    				}else{
	    					stu='';
	    				}
						//获取任务简易难度
	    				switch(v['count']['diff']){
		    				case '1':
		    					level='<span class="task_span c_green">简单</span>';
		    					break;
		    				case '2':
		    					level='<span class="task_span">一般</span>';
		    					break;
		    				case '3':
		    					level='<span class="task_span c_pink">困难</span>';
		    					break;
		    				default:;break;
	    				}
	    				//获取任务奖励
	    				subpoint=v['count']['reward']/100;
					}
					fircon +='<li class="task_li">'
						+'<a href="getOneTask?id='+v['taskid']+'">'
							+'<span class="t_img"><img src="'+v['img']+'"></span>'
							+'<div class="task_d">'
								+'<p class="task_p">'
								+v['name']+stu
								+'</p>'
								+'<p class="task_p chenghei">任务级别   :'
								+level
								+'</p>'
								+'<p class="task_p c_color">奖励   :  <span class="color_span">'
								+subpoint	
								+'</span>子金点A</p>'
								+'<span class="tpos">任务详情</span>'
							+'</div>'
						  +'</a>'
					    +'<li>';
					
				});
			}else{
				fircon='<div class="task_d nohave_task">暂无该类任务</div>';
			}
			
			$('.one_ul').html(fircon);
			if(response['data']['mwc'].length>0){
				$.each(response['data']['mwc'],function(k,v){
					//获取任务循环状态
					if(v['count']){
						//获取任务是否是循环或者限时任务
						if(v['count']['type']==2 && v['count']['cycle']>1){
	    					stu="(循环)";
	    				}else if(v['count']['type']==3){
	    					stu="(限时任务)";
	    				}else{
	    					stu='';
	    				}
						//获取任务简易难度
	    				switch(v['count']['diff']){
		    				case '1':
		    					level='<span class="task_span c_green">简单</span>';
		    					break;
		    				case '2':
		    					level='<span class="task_span">一般</span>';
		    					break;
		    				case '3':
		    					level='<span class="task_span c_pink">困难</span>';
		    					break;
		    				default:;break;
	    				}
	    				//获取任务奖励
	    				subpoint=v['count']['reward']/100;
					}
					twocon +='<li class="task_li">'
						+'<a href="getOneTask?id='+v['taskid']+'">'
							+'<span class="t_img"><img src="'+v['img']+'"></span>'
							+'<div class="task_d">'
								+'<p class="task_p">'
								+v['name']+stu
								+'</p>'
								+'<p class="task_p chenghei">任务级别   :'
								+level
								+'</p>'
								+'<p class="task_p c_color">奖励   :  <span class="color_span">'
								+subpoint	
								+'</span>子金点A</p>'
								+'<span class="tpos">任务详情</span>'
							+'</div>'
						  +'</a>'
					    +'<li>';
					
				});
			}else{
				twocon='<div class="task_d nohave_task">暂无该类任务</div>';
			}
			$('.two_ul').html(twocon);
			if(response['data']['rmrw'].length>0){
				$.each(response['data']['rmrw'],function(k,v){
					//获取任务循环状态
					if(v['count']){
						//获取任务是否是循环或者限时任务
						if(v['count']['type']==2 && v['count']['cycle']>1){
	    					stu="(循环)";
	    				}else if(v['count']['type']==3){
	    					stu="(限时任务)";
	    				}else{
	    					stu='';
	    				}
						//获取任务简易难度
	    				switch(v['count']['diff']){
		    				case '1':
		    					level='<span class="task_span c_green">简单</span>';
		    					break;
		    				case '2':
		    					level='<span class="task_span">一般</span>';
		    					break;
		    				case '3':
		    					level='<span class="task_span c_pink">困难</span>';
		    					break;
		    				default:;break;
	    				}
	    				//获取任务奖励
	    				subpoint=v['count']['reward']/100;
					}
					threcon +='<li class="task_li">'
						+'<a href="getOneTask?id='+v['taskid']+'">'
							+'<span class="t_img"><img src="'+v['img']+'"></span>'
							+'<div class="task_d">'
								+'<p class="task_p">'
								+v['name']+stu
								+'</p>'
								+'<p class="task_p chenghei">任务级别   :'
								+level
								+'</p>'
								+'<p class="task_p c_color">奖励   :  <span class="color_span">'
								+subpoint	
								+'</span>子金点A</p>'
								+'<span class="tpos">任务详情</span>'
							+'</div>'
						  +'</a>'
					    +'<li>';
					
				});
			}else{
				threcon='<div class="task_d nohave_task">暂无该类任务</div>';
			}
			$('.three_ul').html(threcon);
			if(response['data']['ywc'].length>0){
				$.each(response['data']['ywc'],function(k,v){
					//获取任务循环状态
					if(v['count']){
						//获取任务是否是循环或者限时任务
						if(v['count']['type']==2 && v['count']['cycle']>1){
	    					stu="(循环)";
	    				}else if(v['count']['type']==3){
	    					stu="(限时任务)";
	    				}else{
	    					stu='';
	    				}
						//获取任务简易难度
	    				switch(v['count']['diff']){
		    				case '1':
		    					level='<span class="task_span c_green">简单</span>';
		    					break;
		    				case '2':
		    					level='<span class="task_span">一般</span>';
		    					break;
		    				case '3':
		    					level='<span class="task_span c_pink">困难</span>';
		    					break;
		    				default:;break;
	    				}
	    				//获取任务奖励
	    				subpoint=v['count']['reward']/100;
					}
					fourcon +='<li class="task_li">'
						+'<a href="getOneTask?id='+v['taskid']+'">'
							+'<span class="t_img"><img src="'+v['img']+'"></span>'
							+'<div class="task_d">'
								+'<p class="task_p">'
								+v['name']+stu
								+'</p>'
								+'<p class="task_p chenghei">任务级别   :'
								+level
								+'</p>'
								+'<p class="task_p c_color">奖励   :  <span class="color_span">'
								+subpoint	
								+'</span>子金点A</p>'
								+'<span class="tpos">任务详情</span>'
							+'</div>'
						  +'</a>'
					    +'<li>';
					
				});
			}else{
				fourcon='<div class="task_d nohave_task">暂无该类任务</div>';
			}
			$('.four_ul').html(fourcon);
		}else{
			content='<div class="task_d nohave_task">暂无该类任务</div>';
		}
	}
	conv.httpRequest(u,d,f);
}
//效验字符串是否存在数组中
function in_array(stringToSearch, arrayToSearch) {
    for (s = 0; s < arrayToSearch.length; s++) {
     thisEntry = arrayToSearch[s].toString();
     if (thisEntry == stringToSearch) {
      return true;
     }
    }
    return false;
}