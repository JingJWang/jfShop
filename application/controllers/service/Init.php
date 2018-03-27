<?php
defined('BASEPATH') OR exit('No direct script access allowed');
header("Content-type:text/html;charset=utf-8;");
/*
 * 提供给第三方的接口
 */
class init extends RR_Controller{
    
    /**
     * @abstract 提供给金典的接口
     * 用户资料 包括  余额 冻结余额  浏览记录
     * 接口基础参数
     * @param  $appid       string(16)  用户id
     * @param  $secret      string(64)  用户秘钥
     * @param  $sign        string(32)  签名  
     * @param  $nonce       string(32)  随机字符串
     * @param  $sign_type   string(16)  签名类型
     * @param  $reqtime     int(11)     请求时间
     * 请求数据所需参数
     * @package $uid        int(11)     用户id
     * @param   $mobile     int(11)     手机号码
     * @return  json字符串
    */
    function info(){
        $data=$this->input->post();
        //校验参数 签名
        $this->checkParam($data);
        //校验用户状态
        $this->checkUser();
    }
    /**
     * 校验传递的请求参数
     * 参数错误码返回为41011
     */
    function checkParam($data){        
        if(!isset($data['sign']) || empty($data['sign'])  ){
            Often::output('41011','','','请求参数不正确!');
        }
        if(!isset($data['nonce']) || empty($data['nonce'])  ){
            Often::output('41011','','','请求参数不正确!');
        }
        $signdata['nonce']=$data['nonce'];
        if(!isset($data['sign_type']) || empty($data['sign_type']) ){
            Often::output('41011','','','请求参数不正确!');
        }
        $signdata['sign_type']=$data['sign_type'];
        if(!isset($data['reqtime']) || empty($data['reqtime'])  ){
            Often::output('41011','','','请求参数不正确!');
        }
        $signdata['reqtime']=$data['reqtime'];
        if(!isset($data['uid']) || empty($data['uid']) ){
            Often::Output('41011','','','请求参数不正确!');
        }
        $signdata['uid']=$data['uid'];
        if(!isset($data['mobile']) || empty($data['mobile']) ){
            Often::Output('41011','','','请求参数不正确!');
        }
        $this->mobile=$data['mobile'];
        $signdata['mobile'] = $data['mobile'];
        $signdata['appid']  = $this->config->item('jindian_appid');
        $signdata['secret'] = $this->config->item('jindian_secret');
        //验证签名
        $sign=$this->sign($signdata);
        if($sign != $data['sign']){
            Often::output('41011','','','签名错误!');
        }
    }
    
    /**
     * 签名函数
     */
    function sign($data){
        //参数按照asiic 排序
        ksort($data);
        $str='';
        foreach ($data as $key=>$val){
            $str.= $key.'='.$val.'&';
        }
        $str=trim($str,'&');
        $sign=md5($str);
        return $sign;
    }
    /**
     * 手机号验证
     */
    function checkMobile(){
        $mobile=$this->mobile;
        //校验手机号码
        if(!preg_match("/^1[34578]{1}\d{9}$/",$mobile)){
            Often::output('41011','','','手机号码不正确!');
        }
        return $mobile;
    }
    /**
     * 校验请求的用户是否存在并且状态是否正确
     */
    function checkUser(){
        //验证用户是否存在  并且状态是正常用户
        $this->mobile=$this->checkMobile();
        //通过手机号获取用户是否存在并验证状态是否正常
        $this->load->model('service/User_model');
        $this->User_model->mobile=$this->mobile;
        $result=$this->User_model->checkUser();
        if($result=='' || $result == false){
            Often::output('41011','','','无法获取该用户信息!');
        }else if($result && $result['user_status']==-1){
            Often::output('41011','','','该用户状态异常!');
        }
        //验证用户通过 读取相关的数据
        $this->userInfo();
    }  
    
    /**
     * 根据手机号码读取用户的 余额  冻结金额  浏览记录
     */
    function  userInfo(){
     //通过用户手机号读取用户的余额  冻结金额
      $this->load->model('service/User_model');
      $this->User_model->mobile=$this->mobile;
      $result=$this->User_model->userInfo();
      $balance=$result['balance'];//余额
      $fzbalance=$result['fzbalance'];//冻结余额
     //通过用户手机号 获取用户id后读取用户的浏览记录
      //$userid=$result['userid'];
      $userid=1;
      $this->User_model->userid=$userid;
      $recordslist=$this->User_model->getInfoPro();
      $info=array(
          'balance'=>$balance,//余额
          'fzbalance'=>$fzbalance,//冻结金额
          'list'=>$recordslist
      );
      Often::output(41010,'','',$info);
    }
}
