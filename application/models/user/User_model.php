<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * 
 * @author xiaotao
 * 用户模块
 */
class  User_model extends CI_Model{
    //用户id
    public   $user_id               =  '';
    //绑定其他平台的ID
    public   $jindian_id            =  '';
    //电话
    public   $user_phone            =  '';
    //余额
    public   $user_balance          =  '';
    //冻结余额
    public   $user_freeze_balance   =  '';
    //加入时间
    public   $user_jointime         =  '';
    //修改时间
    public   $user_uptime           =  '';
    //状态
    public   $user_status           =  '';
    //用户表名
    private  $conv_user             =  'conv_user';
    
    function __construct(){
        
        parent::__construct();
        $this->load->database();
        if(empty($this->user_jointime)){
             $this->user_jointime=time();
        }
        if(empty($this->user_uptime)){
            $this->user_uptime=time();
        }
        if(empty($this->user_status)){
            $this->user_status=1;
        }
    }
    
    /**
     * @abstract  校验用户是否存在  
     * @param   $user_phone  用户手机号码
     * @return  bool 
     */
    function  isUser(){
        $sql='select 
                user_id,
                user_phone,
                user_status 
              from '.$this->conv_user.' 
              where  user_phone="'.$this->user_phone.'"';
        $query=$this->db->query($sql);
        if($query === false){
            return false;
        }
        $num=$query->num_rows();
        if($num < 1){
            return 0;
        }
        $result=$query->row_array();
        return  $result;
    }
    /**
     * @abstract 添加用户
     * @return bool
     */
    function saveUser(){
        $data=array(
                'user_phone'=>$this->user_phone,
                'user_jointime'=>$this->user_jointime,
                'user_status'=>$this->user_status
        );
        $query=$this->db->insert($this->conv_user,$data);
        if($query === FALSE){
            return   false;
        }
        $id=$this->db->insert_id();
        $resp=array(
               // 'user_id'=>$this->user_id,
                'user_id'=>$id,
                'user_phone'=>$this->user_phone,
                'user_status'=>$this->user_status
        );
        return $resp;
    }
}