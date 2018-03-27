<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * 
 * @author sbk
 * @abstract 任务日志模块
 * 
 */
class  tasklog_model extends CI_Model{
    
    
    public   $user_id           =   '';//用户id
    
    public   $task_invi    =   '';//用户的邀请码 
    
    private  $task_log      =  'conv_task_log';//用户做任务的记录表
    
    function __construct(){
        parent::__construct();
        $this->load->database();
    }
    /**
     * 通过任务用户id 和邀请码  查看任务日志条数
     */
    function selectUserLog(){
       $sql='select * from '.$this->task_log.' a where 
           a.log_status!=2 and user_id='.$this->yquserid;
       $query=$this->db->query($sql);
       $result=$query->result_array();
       if($result){
          return $result;
       }else{
          return  0;
       } 
    }
    /**
     * 通过任务用户id 查看所有的该用户的任务日志
     */
    function selectTaskLog(){
        $sql='select * from '.$this->task_log.
             ' a where a.user_id='.$this->yquserid;
        $query=$this->db->query($sql);
        $result=$query->result_array();
        if($result){
            return $result;
        }else{
            return  false;
        }
    }
    
    /**
     * @abstract 用户分享任务后 添加日志
     */
    function insertTaskLog(){
        $sql='insert into '.$this->task_log.
     		'(user_id,task_id,log_content,log_jointime,log_status) ' .
     		'values '.$this->cont;
        $query=$this->db->query($sql);
        $row=$this->db->affected_rows();
        if($row>1){
            return true;
        }else{
            return false;
        }
    }
    /**
     * @abstract 用户领取了任务后 新用户点击后邀请注册成功后 生成一条任务日志
     */
    function addNowTaskLog(){
        $data=array(
            'user_id'=>$this->userid,
            'task_id'=>$this->taskid,
            'log_content'=>$this->cont,
            'log_jointime'=>time()
        );
        $query=$this->db->insert($this->task_log,$data);
        $row=$this->db->affected_rows();
        if($row==1){
            return true;
        }else{
            return false;
        }
    }
    /**
     * @abstract 用户分享后 修改任务日志的状态为进行中
     */
     function upLogTaskStatus(){
        $outSenate=array(
            'user_id'=>$this->userid,
            'task_id'=>$this->taskid
        );
        $this->db->update($this->task_log,array('log_status'=>1),$outSenate);
        $row=$this->db->affected_rows();
        if($row==1){
            return true;
        }else{
            return false;
        }
     }
    /**
     * @abstract 新用户注册后 修改邀请者的任务日志记录内容
     */
    function upUserTaskLogCon(){
        $intoSenate=array(
            'log_content'=>$this->content
        );
        $outSenate=array(
            'user_id'=>$this->userid,
            'task_id'=>$this->taskid,
            'log_status'=>-1
        );
        $this->db->update($this->task_log,$intoSenate,$outSenate);
        $row=$this->db->affected_rows();
        if($row==1){
            return true;
        }else{
            return false;
        }
    }
    /**
     * @abstract 任务完成后 修改任务日志的数据状态
     */
    function upLogStatus(){
        $intoSenate=array(
            'log_status'=>1
        );
        $outSenate=array(
            'user_id'=>$this->userid,
            'task_id'=>$this->taskid,
        );
        $this->db->update($this->task_log,$intoSenate,$outSenate);
        $row=$this->db->affected_rows();
        if($row==1){
            return true;
        }else{
            return false;
        }
    }

    /**
     * @abstract 修改用户日志表中正在执行任务中的邀请人数
     */
    function upLogPerson(){
        $sql='update '.$this->task_log.' set log_person=
            concat(\''.$this->logperson.'\',log_person),log_updatetime='.time().'
                where log_status!=2 and  user_id="'.$this->userid.'"';
        $query=$this->db->query($sql);
        $row=$this->db->affected_rows();
        if($row>=1){
            return $row;
        }else{
            return false;
        }
    }

    /**
     * @abstract 任务完成后 修改用户任务状态 改为2
     */
    function upTaskStatus(){
        $sql='update '.$this->task_log.' set log_status=2
              where task_id="'.$this->taskid.'" and user_id="'.$this->userids.'"';
        $query=$this->db->query($sql);
        $row=$this->db->affected_rows();
        if($row>=1){
            return $row;
        }else{
            return false;
        }
    }
}