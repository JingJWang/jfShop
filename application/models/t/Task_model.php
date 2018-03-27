<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * 
 * @author sbk
 * @abstract 任务模块
 * 
 */
class  task_model extends CI_Model{
    
    
    public   $user_id           =   '';//用户id
    
    public   $task_status    =   '';//读取数据需要的状态 
    
    private  $task_list         =  'conv_task_list';//任务表
    
    private  $user_task   =  'conv_user_task';//用户做任务的结果表
    function __construct(){
        parent::__construct();
        $this->load->database();
    }
    /**
     * @abstract  读取任务列表
     * @param  string  state  状态  default 默认地址   all 全部任务 
     * @return array | bool
     */
    function getTaskList(){
        $sql='select 
            a.task_id as taskid,
            a.task_name as name,
            a.task_img as img,
            a.task_count as count,
            a.task_starttime as starttime,
            a.task_endtime as endtime
            from '.$this->task_list.' as a
            where  a.task_status = 1  order by a.task_jointime desc ';    
        $query=$this->db->query($sql);
        if($query == false){
            return false;
        }
        if($query->num_rows() < 1){
            return '';
        }
        $result=$query->result_array();
        foreach($result as $key=>$val){
            $result[$key]['count']=json_decode($result[$key]['count'],true);
        }
        return  $result;          
        
    }
    /**
     * 通过任务id 查看单个任务详情
     */
    function selectOneTask(){
       $sql='select 
           task_id as taskid,
           task_name as taskname,
           task_img  as img,
           task_count  as count,
           task_starttime as starttime,
           task_endtime as endtime
           from '.$this->task_list.' a where a.task_status=1 and a.task_id='.$this->taskid;
       $query=$this->db->query($sql);
       $result=$query->row_array();
       if($result){
           $result['count']=json_decode($result['count'],true);
          return $result;
       }else{
          return  false;
       } 
    }
    /**
     * @abstract 通过用户id 查询当前用户做任务的状况
     */
    function getUserTask(){
       $sql='select 
            utask_id as id,
            user_id as userid,
            utask_invital as invital,
            utask_subpoint as subpoint,
            utask_frebanlance as frebanlance,
            utask_authsign as authsign
            from '.$this->user_task.' where user_id='.$this->userid;
        $query=$this->db->query($sql);
        $result=$query->row_array();
        return $result;
    }
    /**
     * @abstract 通过用户邀请码 查询当前用户做任务的状况
     */
    function getInviTask(){
        $sql='select
            utask_id as id,
            user_id as usersid,
            utask_invital as invital,
            utask_subpoint as subpoint,
            utask_frebanlance as frebanlance,
            utask_authsign as authsign
            from '.$this->user_task.' where utask_invital="'.$this->invi.'"';
        $query=$this->db->query($sql);
        $result=$query->row_array();
        if(!empty($result['conten'])){
            $result['conten']=json_decode($result['conten'],true);
        }
        return $result;
    }
   
    /**
     * @abstract 用户进入任务详情后 如果用户邀请码是空的 就添加邀请码
     */
    function upUserInvi(){
        $sql='update '.$this->user_task.' set utask_invital="'.
               $this->invi.'" ,utask_updatetime='.time().' where user_id='.$this->userid;
        $query=$this->db->query($sql);
        if($query){
            return true;
        }else{
            return false;
        }
    }
    /**
     * @abstract 添加用户任务表数据
     */
    function insertUserTask(){
        //添加用户任务表
        $task_data=array(
            'user_id'=>$this->userid,
            'utask_invital'=>$this->invi,
            'utask_jointime'=>time()
        );
        $query=$this->db->insert($this->user_task,$task_data);
        $task_row=$this->db->affected_rows();
        if($task_row==1){
            return true;
        }else{
            return false;
        }
    }
    /**
     * @abstract 新用户通过推广链接 获取0.5子金点A 就需要给新用户添加用户任务表中用户 和子金点数据
     */
    function insertSubPoint(){
        $data=array(
            'user_id'=>$this->userids,
            'utask_frebanlance'=>$this->utask_frebanlance,
            'utask_authsign'=>-1,
            'utask_jointime'=>time()
         );
        $query=$this->db->insert($this->user_task,$data);
        $row=$this->db->affected_rows();
        if($row!=1){
            return false;
        }else{
            return true;
        }
    }
    /**
     * @abstract 用户点击推广链接输入手机号领取福利时 需要给邀请者相应的福利 但该福利需要不能使用 存放到冻结的子金点A里面
     */
    function addUpSubPoint(){
        $sql='update '.$this->user_task.' set utask_subpoint=utask_subpoint+'.$this->point.',
              utask_updatetime='.time().' where utask_invital="'.$this->invi.'"';
        $query=$this->db->query($sql);
        if($query){
            return true;
        }else{
            return false;
        }
    } 
    /**
     * @abstract 新用户注册认证成功后 通过邀请码修改邀请者的可用的子金点A
     */
    function upUserSubPoint(){
      $sql='update '.$this->user_task.' set utask_subpoint=
                utask_subpoint+50,utask_frebanlance=utask_frebanlance-50,utask_updatetime='.time().' 
                where utask_frebanlance>=50 and utask_invital="'.$this->invi.'"';
        $query=$this->db->query($sql);
        if($query){
            return true;
        }else{
            return false;
        }
    }
    /**
     * @abstract 新用户注册认证成功后 通过用户id修改被邀请者的可用的子金点A
     */
    function upUserIdSubPoint(){
       $sql='update '.$this->user_task.' set utask_subpoint=
                utask_subpoint+50,utask_frebanlance=utask_frebanlance-50
                ,utask_updatetime='.time().',utask_authsign=1 
                where utask_frebanlance>=50 and user_id="'.$this->userid.'"';
        $query=$this->db->query($sql);
        if($query){
            return true;
        }else{
            return false;
        }
    }
    /**
     * @abstract 如果新用户在注册后7天内没有认证 就取消新用户的冻结不可用子金点A
     */
    function cancleFrePoint(){
        $sql='update '.$this->user_task.' set utask_frebanlance=
                utask_frebanlance-50,utask_updatetime='.time().' 
                where utask_frebanlance>=50 and user_id="'.$this->userid.'"';
        $query=$this->db->query($sql);
        if($query){
            return true;
        }else{
            return false;
        }
    }
    /**
     * @abstract 生成随机邀请码
     * @return string;
     */
    function createNonceStr($length = 6) {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }
}