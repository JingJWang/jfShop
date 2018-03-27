<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * 
 * @author mxt
 * @abstract 地址管理模块 oop
 * 
 */
class  Address_model extends CI_Model{
    
    
    public   $user_id           =   '';//用户id
    
    public   $address_status    =   '';//读取数据需要的状态
    
    public   $address_name      =   '';
    
    private  $user_address      =  'conv_user_address';//用户收货地址表
    
    function __construct(){
        parent::__construct();
        $this->load->database();
    }
    /**
     * @abstract  读取地址列表
     * @param  int     userid   用户id
     * @param  string  state  状态  default 默认地址   all 全部地址 
     * @return array | bool
     */
    function selAddressList(){
        $this->address_status == 'default' ? $state = ' and address_status in (2)': '';
        $this->address_status == 'all' ? $state = ' and address_status in (1,2)': '';
        $sql='select address_id as address_id,user_id as userid,address_name as name,address_phone as phone,address_county as county,
              address_province as province,address_city as city,address_area as area,address_details as details,
              address_jointime as jointime,address_updatetime as address_updatetime,address_status as status from '.$this->user_address.'
              where  user_id = '.$this->user_id.$state.' order by address_status desc ';    
        $query=$this->db->query($sql);
        if($query == false){
            return false;
        }
        if($query->num_rows() < 1){
            return 0;
        }
        if($this->address_status == 'all'){
            $result=$query->result_array();
        }
        if($this->address_status == 'default'){
            $result=$query->row_array();
        }
        return  $result;          
        
    }
    /**
     * 
     */
    function getList(){
        $sql='select address_id,user_id,address_name,address_phone,address_county,
              address_province,address_city,address_area,address_details,
              address_jointime,address_updatetime,address_status
              from '.$this->user_address.'
              where  user_id = '.$this->user_id.' and address_status in '.$state;
        $query=$this->db->query($sql);
        if($query == false){
            return false;
        }
        if($query->num_rows() < 1){
            return 0;
        }
        if($this->address_status == 'all'){
            $result=$query->result_array();
        }
        if($this->address_status == 'default'){
            $result=$query->row_array();
        }
        return  $result;
        
    }

    /**
     * 通过地址id 查看单个地址
     */
    function selectOneAdd(){
       $sql='select address_id as addid,user_id as userid,address_name as name,address_phone as phone,address_county as county,
          address_province as province,address_city as city,address_area as area,address_details as details,
          address_jointime as jointime,address_updatetime as updatetime,address_status as status 
          from '.$this->user_address.' a where a.address_status >0 and a.address_id='.$this->addrId.' and a.user_id='.$this->userid;
       $query=$this->db->query($sql);
       $result=$query->row_array();
       if($result){
          return $result;
       }else{
          return  false;
       } 
    }
    /**
   *  修改地址
   */
   function updateAddress(){
      switch($this->sign){
        case 'del':
        //假删数据
          $data=array(
                'address_status'=>$this->status
              );
          break;
        case 'edit':
        //修改地址
          $data=array(
                'address_name'=>$this->name,
                'address_phone'=>$this->phone,
                'address_province'=>$this->province,
                'address_city'=>$this->city,
                'address_area'=>$this->area,
                'address_details'=>$this->details,
                'address_updatetime'=>time()              
              );
          break;
        case 'default':
        //修改为默认地址
          $data=array(
                'address_status'=>'2'
              );
          break;
        default:;
      }
      $this->db->trans_begin();
      //如果状态为3 则为选择默认地址 
      if($this->sign =='default'){//修改默认地址
          //查看当前需要修改的地址状态是否是2 默认 如果是默认 无需修改 直接返回ture
        $this->addrId=$this->addid;
        
        $res=$this->selectOneAdd();
        //验证获取结果并判断地址状态是否为2
         if($res && $res['status']==2){
          return true;
        }else{ 
            //通过当前id查找的地址状态是否是默认为2 如果不是2 那就先把该用户地址为2的 把状态2改成1 然后通过传过来的id的地址状态改成2
            //通过地址id获取用户userid
            $this->userid=$res['userid'];
            //查看该用户是否有默认地址  如果有默认地址
            $this->address_status='default';
            $this->user_id=$this->userid;
            $resut=$this->selAddressList();
            //该用户有默认地址 并且当前修改修改的默认地址 并不是默认地址
            if($resut){
                //先把默认地址状态改成非默认地址
                $this->db->update($this->user_address,array('address_status'=>'1'),array('address_id'=>$resut['address_id']));
                $row=$this->db->affected_rows();
                if($row == 1){
                    $this->db->update($this->user_address,$data,array('address_id'=>$this->addid,'user_id'=>$this->userid));
                    $rows=$this->db->affected_rows();
                    if($rows == 1){
                        $this->db->trans_commit();
                        return true;
                    }else{
                        $this->db->trans_rollback();
                        return false;
                    }
                }else{
                    $this->db->trans_rollback();
                    return false;
                }
            }else{
                //该用户没有默认地址
                //直接修改当前地址为默认地址
                $this->db->update($this->user_address,array('address_status'=>'2','address_updatetime'=>time()),array('address_id'=>$this->addid,'user_id'=>$this->userid));
                $rows=$this->db->affected_rows();
                if($rows == 1){
                    $this->db->trans_commit();
                    return true;
                }else{
                    $this->db->trans_rollback();
                    return false;
                }
            }
        } 
      }else{
          $this->db->update($this->user_address,$data,array('address_id'=>$this->addid,'user_id'=>$this->userid));
          $rowe=$this->db->affected_rows();
          if($rowe == 1){
              $this->db->trans_commit();
              return true;
          }else{
              $this->db->trans_rollback();
              return false;
          }
      }
   }
}