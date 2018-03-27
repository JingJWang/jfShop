<?php
defined('BASEPATH') OR exit('No direct script access allowed');
header("Content-type:text/html;charset=utf-8;");
class init extends RR_Controller{
    /**
     * @abstract 显示任务首页
     */
    function proList(){
        $this->display('promotion/pro.html');
    }
    /**
     * @abstract 任务列表首页-获取任务列表内容
     */
    function getList(){
        $task['mwc']=array();
        $task['ywc']=array();
        $task['rmrw']=array();
        //获取任务列表
        $this->load->model('t/Task_model');
        $taskList=$this->Task_model->getTaskList();
        $task['task']=array_column($taskList,null,'taskid');
        //通过任务日志获取进行中的任务以及状态
        $this->load->model('t/Tasklog_model');
        $this->Tasklog_model->yquserid=$this->userid;
        $taskList=$this->Tasklog_model->selectTaskLog();
        $log=array_column($taskList,'log_status','task_id');
        foreach ($log as $key=>$val){
            if($val==2){
                $task['ywc'][]=$task['task'][$key];
            }else{
                $task['mwc'][]=$task['task'][$key];
            } 
        }
        foreach($task['task']  as $key=>$val){
            if($task['task'][$key]['count']['type']==3){
                $task['rmrw'][]=$val;
            }
        }
        Often::Output($this->config->item('trues'),'','',$task);
    }
    /**
     * 效验参数 id
     */
    function checkTaskId(){
        //效验参数
        $id=$this->input->get('id',true);
        if(empty($id) || !is_numeric($id) || $id<0){
            Often::Output($this->config->item('false'),'非法参数');
        }
        return $id;
    }
    /**
     * @abstract 通过任务id 获取当个任务详情
     */
    function getTask(){
        //效验参数
        $id=$this->checkTaskId();
        //获取任务详情
        $this->load->model('t/Task_model');
        $this->Task_model->taskid=$id;
        $task=$this->Task_model->selectOneTask();
        if(!$task){
            return false;
        }
        return $task;
    }
    /**
     * @abstract 任务模块-查看任务详情
     * id是任务id
     */
    function getOneTask(){
       $task=$this->getTask();
       if($task){
           switch($task['count']['diff']){
               case '1':
                   $task['diff']='简单';
                   break;
               case '2':
                   $task['diff']='一般';
                   break;
               case '3':
                   $task['diff']='困难';
                   break;
               default:;break;
           }
           switch($task['count']['type']){
               case '1':
                   $task['type']='单次';
                   break;
               case '2':
                   $task['type']='循环';
                   break;
               case '3':
                   $task['type']='限时';
                   break;
               default:;break;
           }
           //获取当前用户的邀请码
           $task['cycle']=$task['count']['cycle'];
           $task['reward']=$task['count']['reward']/100;
           $task['explan']=$task['count']['explan'];
           $task['process']=$task['count']['process'];
       }
       //用户进入到任务详情时 判断用户任务表中是否存有个人记录 如果没有 就添加一条用户任务记录及邀请码
       //如果有 就查看是否邀请码  如果有邀请码 返回ture 如果没有邀请码 就生成邀请码 添加 并返回true;
       $this->insertUserTask($task['taskid']);
       //获取用户任务记录的邀请码
       $result=$this->selecUserTask();
       $task['invi']=$result['invital'];
       //任务分享后需要跳转的链接
       $param='zonghe='.$task['taskid'].'_'.$task['invi'];
       $task['taskshareurl']='http://z.90storm.com/t/init/shareHtml?'.$param;
       //获取公众号的appid
       $task['APPID']=$this->config->item('appid');
       //分享成功后需要修改任务用户表该用户的记录
       $task['homeImg']="http://z.90storm.com/t/init/upUserTask";
       //分享的图标
       $task['shareImg']="http://z.90storm.com/static/promotion/img/hstlogo.png";
       //分享内容时需要获取的账户信息
       $this->load->model('t/Wxcode_model');
       $task['signPackage']=$this->Wxcode_model->GetSignPackage();
       //赋值 输出
       $this->assign('result',$task);
       $this->display('promotion/taskMess.html');
    }
    /**
     * @abstract 用户进入到任务详情页面 先查询该用户是否在用户任务表中已经存有数据
     */
    function selecUserTask(){
        $this->load->model('t/Task_model');
        $this->Task_model->userid=$this->userid;
        $result=$this->Task_model->getUserTask();
        if($result){
            return $result;
        }else{
            return false;
        }
    }
    /**
     * @abstract 用户进入到任务详情页面 就给该用户添加一条任务用户数据
     */
    function insertUserTask($taskid){
        //用户进入到任务详情页面 先判断在用户任务表中是否存有数据 如果存有数据 就不添加数据 
        //添加数据是为了保证用户的邀请码只有一个 该用户任务表中 一个用户只有一条记录
        $res=$this->selecUserTask();
        //如果查询任务用户表 没有记录 就生成一条记录
        $this->load->model('t/Task_model');
        if(!$res){
            //获取随机邀请码
            $invi=$this->Task_model->createNonceStr();
            $this->Task_model->invi=$invi;
            $this->Task_model->userid=$this->userid;

            $task=$this->Task_model->insertUserTask();
            return $task;
        }else if ($res && empty($res['invital'])){//如果用户任务表中有记录 但是用户邀请码为空 那就修改用户任务表的邀请码
            //获取随机邀请码
            $invi=$this->Task_model->createNonceStr();
            $this->Task_model->invi=$invi;
            $this->Task_model->userid=$this->userid;
            $task=$this->Task_model->upUserInvi();
            if($task){
                return true;
            }else{
                return false;
            }
        }else{
            return true;
        }
    }
    /**
     * @abstract 效验参数
     */
    function checkInvi(){
        $id=$this->input->post('id');
        if(empty($id) || !is_numeric($id) || $id<0){
            Often::Output($this->config->item('false'),'非法参数');
        }
        $new['id']=$id;
        $invi=$this->input->post('suiji');
        if(!isset($invi) || !is_string($invi) || empty($invi) || strlen($invi)!=6){
             Often::Output($this->config->item('false'),'非法参数');
        }
        $new['invi']=$invi;
        return $new;
    }
    /**
     * @abstract  查询任务列表中 所有邀请注册任务的总条数
     */
     function getTaskList(){
     	$this->load->model('t/Task_model');
     	$this->Task_model->status='all';
     	$result=$this->Task_model->getTaskList();
     	return $result;
     }
    /**
     * @abstract用户领取该任务后 储存用户的邀请码,并添加该用户领取了该任务的状态
     */
    function upUserTask(){
        $new=$this->checkInvi();
        //获取任务列表中所有邀请注册任务的总条数
        $taskCount=$this->getTaskList();
		//把获取的任务列表将为一维数组 并且任务id 作为key 任务详情为val        
        $taskList=array_column($taskCount,'count','taskid');
        //通过邀请码 查询用户的userid
        $this->load->model('t/Task_model');
        $this->Task_model->invi=$new['invi'];
        $userTask=$this->Task_model->getInviTask();
        $taskUse=$userTask['usersid'];
        //3.查找用户做日志的记录条数
        $this->load->model('t/Tasklog_model');
        $this->Tasklog_model->yquserid=$taskUse;
        $numresult=$this->Tasklog_model->selectUserLog();
        //把任务日志里面当前用户的任务日志数据将为一维数组 以task_id 为key 任务状态为val
        $logList=array_column($numresult,'log_status','task_id');
        $insert='';
        //对比任务列表和任务日志类别中的任务id 看是否存在差集
        $merge=array_diff_key($taskList,$logList);
       //var_dump($merge);exit;
        //没有差集 并且任务条数不相等  说明当前该任务日志列表没有该用户数据  就添加该用户数据
        if($merge==null && count($taskCount)!=count($numresult)){
        	foreach($taskCount as $key=>$val){
        		$userid=$taskUse;//用户useid
	    		$taskid=$val['taskid'];//任务id
	    		$cont='"用户'.$userid.'在'.time().'领取了任务'.$taskid.'"';//用户领取任务拼接
	    		$status=-1;//领取任务状态
    			if($val['taskid']==$new['id']){
    				$status=1;
        		}
        		$insert .='('.$userid.','.$taskid.','.$cont.','.time().','.$status.')'.',';//要添加到sql 的内容弄拼接 没有做最后一步处理
        	}
        	//添加日志记录
				$this->Tasklog_model->cont=substr($insert,0,strlen($insert)-1).';';
        		$numresult=$this->Tasklog_model->insertTaskLog();
        		if($numresult){
        			Often::Output($this->config->item('trues'),'添加日志成功，');
        		}
        		Often::Output($this->config->item('false'),'生成任务日志失败');
        }else if($merge==null && count($taskCount)==count($numresult)){
        	//任务条数想等 所有的任务都已经生产任务日志了
        	//验证当前任务id 是否是进行中的任务
        	if($logList[$new['id']]!=1){
        		//任务没有在执行当中 修改任务的状态
		 	    $this->Tasklog_model->taskid=$new['id'];
			    $this->Tasklog_model->userid=$taskUse;
			    $result=$this->Tasklog_model->upLogTaskStatus();
			    if($result){
			    	Often::Output($this->config->item('trues'),'修改任务日志成功');
			    }else{
			    	Often::Output($this->config->item('false'),'修改任务日志失败');
			    }
        	}else{
        		Often::Output($this->config->item('trues'),'该任务已经在进行中了');
        	}
        }else{
        	//任务列表和任务日志有差集  
        	//第一步 先判断差集结果里面是否有当前的任务id 
        	//1.1如果当前任务id 说明该任务id没有任务日志 需要添加差集的任务id到任务日志里面
        	foreach($merge as $key=>$val){
        		$userid=$taskUse;//用户useid
        		$status=-1;//领取任务状态
    			$cont='"用户'.$userid.'在'.time().'领取了任务'.$key.'"';//用户领取任务拼接
        		if($key==$new['id']){//当前任务id 存在于差集中
        			$status=1;
        		}
        		 $insert .='('.$userid.','.$key.','.$cont.','.time().','.$status.')'.',';
        	}
        	//添加日志记录
			$this->Tasklog_model->cont=substr($insert,0,strlen($insert)-1).';';
			$numresult=$this->Tasklog_model->insertTaskLog();
			if($numresult){
				Often::Output($this->config->item('trues'),'添加日志成功，');
			}
			Often::Output($this->config->item('false'),'生成任务日志失败');
        }
    }
    /**
     * @abstract 用户的推广链接 及二维码
     */
    function codeOrLink(){
        $task=$this->getTask();
        $this->load->model('t/Task_model');
        $this->Task_model->taskid=$task['taskid'];
        //通过用户id 获取用户邀请码
        $this->Task_model->userid=$this->userid;
        $result=$this->Task_model->getUserTask();
        //'.$_SERVER['HTTP_HOST'].'
        $url='http://z.90storm.com/t/init/shareHtml?zonghe='.$task['taskid'].'_'.$result['invital'];
        $this->assign('url',$url);
        $this->display('promotion/tg.html');
    }
    /**
     * 手机号验证
     */
    function checkData(){
       $mobile=$this->mobile;
        //校验手机号码
        if(preg_match("/^1[34578]{1}\d{9}$/",$mobile)==0){
            Often::output('30011','手机号码不正确!','','');
        }
        return $mobile;
    }
    /**
     * 效验传入的参数 手机号 和zonghe 参数
     */
    function checkJinData(){
        $data=$this->input->post();
        //效验手机号码
        $this->mobile=$data['username'];
        $mobile=$this->checkData();
        $zonghe=explode('_',$data['zonghe']);
        if(!isset($zonghe[0]) || empty($zonghe[0]) || $zonghe[0]<0 || !is_numeric($zonghe[0])){
            Often::Output($this->config->item('false'),'非法任务id');
        }
        if(!$zonghe[1] || empty($zonghe[1]) || !is_string($zonghe[1]) || strlen($zonghe[1])!=6){
            Often::Output($this->config->item('false'),'非法邀请码');
        }
        $zonghe['mobile']=$mobile;
        return $zonghe;
    }
    /**
     * @abstract 被邀请者点击链接 输入手机号后 给这个新用户生成一条记录在用户任务表中 
     */
    function shareInsUserTask(){
        //效验数据
        $zonghe=$this->checkJinData();
        //通过新邀请的用户输入的手机号码 储存到任务用户表中,并且该用户的数据状态为-1
        $this->load->model('user/User_model');
        $this->User_model->user_phone=$zonghe['mobile'];
        $chekUser=$this->User_model->isUser();
        if($chekUser){
            //用户手机号码存储 不能领取福利
            Often::Output($this->config->item('false'),'该手机号码已经存在');
        } 
        $url='http://106.38.36.98:9080/sso/register.html';
        Often::Output($this->config->item('trues'),'',$url,$zonghe['mobile']);
    }
    /**
     * @abstract 分享页面展示
     */
    function shareHtml(){
        $this->display('promotion/share.html');
    }
    /**
     * @abstract 新用户注册成功后 效验参数  调取该方法 效验
     * 
     */
    function checkRegisUse(){
    	//1.先校验传入的参数
        $zonghe=$this->checkJinData();
        //通过传入的参数 向user表和task_user添加相应的数据
        //往user表添加新注册用户数据
        $this->load->model('user/User_model');
        $this->User_model->user_phone=$zonghe['mobile'];
        $chekUser=$this->User_model->isUser();
        if($chekUser){
            //用户手机号码存储 不能领取福利
            Often::Output($this->config->item('false'),'该手机号码已经存在');
        }
        $this->User_model->user_jointime=time();
        $this->User_model->user_status=1;
        $insetUser=$this->User_model->saveUser();
        if($insetUser){
        	//向任务用户表添加记录
        	$this->load->model('t/Task_model');
        	 $this->Task_model->utask_frebanlance=$this->config->item('task_newReg');
			 $this->Task_model->userids=$insetUser['user_id'];//获取用户userid
			 $result=$this->Task_model->insertSubPoint();
			 if($result){
			 	Often::Output($this->config->item('trues'),'添加新用户奖励成功');
			 }
			 Often::Output($this->config->item('false'),'添加新用户奖励失败');
        }else{
        	Often::Output($this->config->item('false'),'添加新用户失败');
        }
        return true;
    }
    /**
     * @abstract 调取金点接口 获取要邀请认证人数情况
     */
    function getInviNum(){
        $option=$this->input->post('zonghe');
        $url='http://106.38.36.98:9080/sso/queryMallRecUserInfo.do?zonghe='.$option;
        $data=json_decode(Often::curlPost($url,$option));
        echo json_encode($data);
    }
    /**
     * @abstract 查看邀请人数
     */
    function  lookInviPeo(){
        $this->display('promotion/rz.html');
    }
    /**
     * @abstract 验证用户的注册时间戳和认证时间差是否合法
     */
    function checkTimeCha(){
        $data=$this->input->post();
        $timestam=array();
        if(is_numeric($data['registime']) && $data['registime']>0 && !empty($data['registime'])){
            $timestam['registime']=$data['registime'];
        }
        if(is_numeric($data['authtime'])  && !empty($data['authtime'])){
            $timestam['authtime']=$data['authtime'];
        }else{
            $timestam['authtime']=0;
        }
        return $timestam['authtime']-$timestam['registime'];
    }
    /**
     * @abstract 金点那边的用户认证城后需要调取的接口
     * 用户认证成功后先判断用户认证的时间和注册账号的时间相差是否超过7天 如果超过7天 双方没有任何奖励
     * 不超过7天  先验证手机号和综合参数需要修改用户所得的子金点A以及邀请者所获得的子金点A
     * 验证参数后 要验证用户和邀请的用户是否都存在表数据
     */
    function authUser(){
        //验证手机号码和综合参数
        $zonghe=$this->checkJinData();
        //通过认证的手机号码 查找用户的userid
        $this->load->model('user/User_model');
        $this->User_model->user_phone=$zonghe['mobile'];
        $chekUser=$this->User_model->isUser();
        //通过userid 查找认证用户的用户任务信息
        $this->load->model('t/Task_model');
        $this->Task_model->userid=$chekUser['user_id'];
        $userTask=$this->Task_model->getUserTask();
        //通过邀请码获取邀请者的个人信息
        $this->load->model('t/Task_model');
        $this->Task_model->invi=$zonghe[1];
        $yqresult=$this->Task_model->getInviTask();
        //验证传入的时间戳,并结算时间戳相差
        $time=$this->checkTimeCha();
        if($time<0){
            Often::Output($this->config->item('false'),'用户未认证');
        }else{
            if($time/86400>8 && $userTask){//用户注册到认证的时间超过7天 没有任何奖励
                //通过userid 获取用户任务表中的数据
                $canclePoint=$this->Task_model->cancleFrePoint();
                if($canclePoint){
                    Often::Output($this->config->item('trues'),'用户未在注册后7天内认证,取消相应的奖励成功');
                }
                Often::Output($this->config->item('trues'),'用户未在注册后7天内认证,取消相应的奖励失败');
            }else if($time/86400<8 && $userTask){
                //1修改认证用户所获得的奖励
                //$this->Task_model->userid=$chekUser['user_id'];
                $upUserIdPoint=$this->Task_model->upUserIdSubPoint();
                if(!$upUserIdPoint){
                    Often::Output($this->config->item('false'),'修改新用户获取的奖励失败,请重新检查数据');
                }
                //2.判断邀请者是否是已认证状态
                if($yqresult['authsign']==1){
                    //用户在7天内认证 ,并且存在用户任务信息表中
                    //2.1修改邀请者的个人信息
                    //2.1.1 先通过 邀请者的userid 获取用户在任务日志表中的数据
                    $this->load->model('t/Tasklog_model');
                    $this->Tasklog_model->yquserid=$yqresult['usersid'];
                    $numresult=$this->Tasklog_model->selectUserLog();
                    //2.1.2  获取任务列表中所有邀请注册任务的总条数
                    $taskCount=$this->getTaskList();
                    //对比任务列表和日志列表中的数据条数以及依据当前的任务id 修正相应的数据 及依旧完成的任务id 添加相应的任务福利
                    $upResult=$this->diffLogUP($taskCount,$numresult,1,$zonghe,$yqresult['usersid']);
                    if($upUserIdPoint){
                        Often::Output($this->config->item('trues'),'新用户在注册后7天内认证,获取相应的奖励成功');
                    }else{
                        Often::Output($this->config->item('false'),'双方在获取相应的奖励失败');
                    } 
                }else{
                    Often::Output($this->config->item('false'),'邀请者没有认证账号,获取奖励失败');
                }
            }
        }
    }
    /**
     * @abstract 对于邀请者的任务日志和任务列表的数据 并修改相应的数据
     * @param $logArray 用户在日志表中的集合
     * @param $taskArray 任务列表结合  
     * @param $num 认证的人数
     * @param $zonghe 新用户手机号  任务id 邀请者的邀请码 status 1为已认证 -1 没有认证
     * @param $yquserid 邀请者userid
     */
    function diffLogUP($taskArray,$logArray,$num=1,$zonghe,$yquserid){
        if(!is_array($zonghe) || !is_array($logArray) || !is_array($taskArray)){
            Often::Output($this->config->item('false'),'非法参数输入');
        }
        //把获取的任务列表将为一维数组 并且任务id 作为key 任务详情为val
        $logList=array_column($logArray,'log_status','task_id');
        //把任务日志里面当前用户的任务日志数据将为一维数组 以task_id 为key 任务状态为val
        $taskList=array_column($taskArray,'count','taskid');
        //对比2个数组的差集
        //对比任务列表和任务日志类别中的任务id 看是否存在差集
        $merge=array_diff_key($taskList,$logList);
        if($merge==null && count($taskArray)==count($logArray)){
            //用户任务表里面存储该任务 
            //任务条数想等 所有的任务都已经生产任务日志了 修改用户任务表中邀请的人数
            $this->load->model('t/Tasklog_model');
            $this->Tasklog_model->logperson=$zonghe['mobile'].';';
            $this->Tasklog_model->userid=$yquserid;
            $yqresult=$this->Tasklog_model->upLogPerson();
            if($yqresult==count($taskArray) && $yqresult==count($logArray)){//修改任务的邀请人数相同 修改成功
                // 获取任务日志表中该用户的每个任务日志所邀请的人数
                foreach($logArray as $key=>$val){
                    $logArray[$key]['num']=count(explode(';',$val['log_person']));
                }
                //处理数组中的字符串 获得邀请的总人数个数 并且作为val 任务id作为key
                $newLogTask=array_column($taskArray,'num','task_id');
                //循环遍历任务日志类别 得到任务邀请的人数 然后减去任务日志中的人数在减去当前认证的人数 如果等于0 说明任务完成 修改该条任务状态
                //并且添加一条该任务日志记录(是循环任务) 如果不等于0 说明没有完成任务
                foreach($taskArray as $key=>$val){
                    if($taskArray[$key]['count']['result']-$logArray[$key]['num']==0){
                        //说明 任务id 为$key 的任务已经完成 
                        //修改任务日志状态
                        $this->Tasklog_model->userids=$yquserid;
                        $this->Tasklog_model->taskid=$logArray[$key]['task_id'];
                        $res=$this->Tasklog_model->upTaskStatus();//修改完成的任务日志状态为2 2表示已完成 1进行中 -1 表示未进行
                        if($res){
                            //如果当前任务是限时任务 任务在完成时间内 就不需要添加任务日志了
                            if($taskArray[$key]['count']['type']!=3){
                                //添加一条正在进行中的任务日志
                                $this->Tasklog_model->cont='用户在'.time().'领取了任务'.$logArray[$key]['task_id'];
                                $insLog=$this->Tasklog_model->addNowTaskLog();
                            }else{
                               $insLog=true;
                            }
                            //给邀请者添加该任务的完成奖励
                            $this->load->model('t/Task_model');
                            $this->Task_model->invi=$zonghe[1];
                            $this->Task_model->point=$taskArray[$key]['count']['reward'];
                            $addupPoint=$this->Task_model->addUpSubPoint();
                            //添加任务成功奖励日志
                            $insAward=$this->insertAwardLog($logArray[0]['user_id'],$logArray[$key]['task_id'],$taskArray[$key]['count']['reward']);
                            if($insLog && $insAward && $addupPoint){
                                return true;
                            }else{
                                return false;
                            }
                        }else{
                            //任务还在进行中 不做任何修改
                            return true;
                        }
                 }else{
                    //任务还在进行中 不做任何修改
                    return true;
                 }
            }
        }
      }
    }
    /**
     * @abstract 发放任务奖励
     * @param $userid 邀请者id
     * @param @taskid 邀请者完成任务的id
     * @param @award 完成该任务所得到的奖励 *100 
     */
    function insertAwardLog($userid,$taskid,$award){
        $this->load->model('t/Taskawardlog_model');
        $this->Taskawardlog_model->userid=$userid;
        $this->Taskawardlog_model->taskid=$taskid;
        $this->Taskawardlog_model->award=$award;
        $insAward=$this->Taskawardlog_model->addAwardLog();
        return $insAward;
    }
}