<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * 
 * @author sbk
 * @abstract 任务日志模块
 * 
 */
class  taskawardlog_model extends CI_Model{
    
    
    public   $user_id           =   '';//用户id
    
    public   $task_id    =   '';//任务id 
    
    private  $award_log      =  'conv_award_log';//任务发放记录
    
    function __construct(){
        parent::__construct();
        $this->load->database();
    }
    /**
     * @abstract 用户领取了任务后 新用户点击后邀请注册成功后 生成一条任务日志
     */
    function addAwardLog(){
        $data=array(
            'user_id'=>$this->userid,
            'task_id'=>$this->taskid,
            'log_award'=>$this->award,
            'log_jointime'=>time()
        );
        $query=$this->db->insert($this->award_log,$data);
        $row=$this->db->affected_rows();
        if($row==1){
            return true;
        }else{
            return false;
        }
    }
}