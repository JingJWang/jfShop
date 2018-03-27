<?php
session_start();
class RR_Controller extends CI_Controller {
    
    protected   $userid='';
    
    protected   $backurl =  '';
    
    protected   $str='';
    
    public function __construct() {
        parent::__construct();
		$this->str=$this->chuliString();
        if(in_array($this->str,$this->config->item('no_login'))==false){
            $this->isOnline();
        }
    }
    /**
     * @abstract 处理获取路径的字符串
     */
    function chuliString(){
        $newUrl=$_SERVER['REQUEST_URI'];
		if(strpos($newUrl,'?')){
			$urlarr=explode('?',$newUrl);
			return $urlarr['0'];
		}else{
			return $newUrl;
		}
    }
    function selsession(){
        var_dump($_SESSION);
    }
    function delsession(){
        session_destroy();
    }
    /**
     * @abstract  校验当前用户是否登录
     */
    public function checkLogin(){
        $this->isOnline();
    }   
    /**
     * @abstract 校验用户是否在线
     */
    function isOnline(){
       $token=$this->input->get('token');
       if(empty($token) && empty($_SESSION['user']) ){
			$this->isRequestAjax();exit();
	   }else{
			if(!empty($token)){
			  $this->isToken($token);
			}
	   }	
        $_SESSION['user']['user_token']=$token;
        $this->userid=$_SESSION['user']['user_id'];
    }
    /**
     * @abstract  用户登录    根据请求方式返回不同的结果 
     */ 
    function isRequestAjax(){
        $req=$this->input->is_ajax_request();
        if(empty($this->backurl)){
            $backurl='http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
        }else{
            $backurl=$this->backurl;
        }
        $option=$this->config->item('jindian_api_sso').'?service='.$backurl;       
        if('ajax' == $req){
            Often::Output('30011','',$option,'');
        }else{
            header('Location:'.$option);exit();
        }
    }
    /**
     * @abstract  登录 校验令牌是否有效
     */
    function isToken($token){
        if(empty($token)){
            exit();
        }
        $backurl='http://'.$_SERVER['HTTP_HOST'];
        $option='?token='.$token.'&service='.$backurl;
        $url=$this->config->item('jindian_api_token').$option;
        $result=Often::curlPost($url, array(''));
        if(empty($result)){
            Often::Output('11011','没有获取到您的登录信息!');
        }
        $resp=json_decode($result);
        if(empty($resp)){
            Often::Output('11011','个人信息获取出现异常!');
        }
        //校验请求返回是否有效
        if('true' == $resp->isval){
            //校验是否存在用户
            $this->isUser($resp->username);
        }else{
            Often::Output('11011','校验令牌出现异常!');
        }
    }
    /**
     * @abstract 登录  校验是否存在用户
     * @param  $mobile  手机号码
     * @return
     */
    function isUser($mobile){
        $this->load->model('user/User_model');
        $this->User_model->user_phone=$mobile;
        $result=$this->User_model->isUser();
        if($result === false){
            exit();
        }
        if($result == 0){
            //当前用户不存保存用户
            $resp=$this->User_model->saveUser($mobile);
            $_SESSION['user']=array(
                    'user_id'=>$resp['user_id'],
                    'user_phone'=>$resp['user_phone'],
                    'user_status'=>$resp['user_status'],
            );
            return true;
        }
        if($result['user_status'] != 1){
            exit('账号异常');
        }
        $_SESSION['user']=array(
                'user_id'=>$result['user_id'],
                'user_phone'=>$result['user_phone'],
                'user_status'=>$result['user_status'],
        );
        return true;
    }
    //模板传值
    public function assign($key,$val){
        $this->rr_smarty->assign($key,$val);
    }
    //调用显示模板
    public function display($html){
        $this->rr_smarty->display($html);
    }
    
    
    
}