<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class  user_model extends CI_Model{
    //用户表
    private     $conv_user ='conv_user';
    //浏览记录表
    private     $conv_source = 'conv_records';
    //回收产品表
    private     $conv_goods = 'conv_goods_copy';
    //商城产品表
    private     $conv_shop = 'conv_shop_copy';
    
    function __construct(){
        parent::__construct();
        $this->load->database();
    }
    /**
     * 通过手机号查找用户是否存在
     */
    function checkUser(){
        $sql='select a.user_status from '.$this->conv_user.' as a  where user_phone ='.$this->mobile;
        $query=$this->db->query($sql);
        $result=$query->row_array();
        if($result){
            return $result;
        }else{
            return  false;
        }
    }
    /**
     * 通过手机号查找用户相关数据
     */
    function userInfo(){
        $sql='select a.user_id as userid, a.user_balance as balance,a.user_freeze_balance as fzbalance 
            from '.$this->conv_user.' as a  where user_phone ='.$this->mobile
            .' and a.user_status=1';
        $query=$this->db->query($sql);
        $result=$query->row_array();
        if($result){
            return $result;
        }else{
            return  false;
        }
    }
    /**
     * 通过获取用户浏览记录的来源来获取产品的信息
     * $val['source'] 1为商城 2为回收
     */
    function getInfoPro(){
        $records=$this->listRecords();
        //回收产品id
        $goodsid='';
        //商城产品id
        $shopid='';
        //声明空数组来接受产品信息
        $data=array();
        //接收处理后的产品信息
        $newdata=array();
        if($records){
            foreach($records as $key=>$val){
                switch ($val['source']){
                    case '1':
                        $shopid .=$val['shopid'].',';
                        break;
                    case '2':
                        $goodsid .=$val['shopid'].',';
                        break;  
                    default:;
                        break;
                }
            }
            $shopid=substr($shopid, 0, -1);
            $goodsid=substr($goodsid, 0, -1);
            //通过获取的商城产品id 获取该产品的信息
            $shop_sql='select a.shop_id as produid,a.shop_name as name,a.shop_img as img
                             from '.$this->conv_shop.' as a where a.shop_status =1 and
                             a.shop_id in ('.$shopid.')';
            $query=$this->db->query($shop_sql);
            $result['shop']=$query->result_array();
            foreach($result['shop'] as &$shoplist){
                $shoplist['state']=1;
            }
            //通过获取的回收产品id 获取该产品的信息
            $shop_sql='select a.goods_id as produid,a.goods_name as name,a.goods_img as img
                             from '.$this->conv_goods.' as a where a.goods_status =1 and
                             a.goods_id in ('.$goodsid.')';
            $query=$this->db->query($shop_sql);
            $result['goods']=$query->result_array();
            foreach($result['goods'] as &$shoplist){
                $shoplist['state']=2;
            }
            $data=array_merge_recursive($result['shop'],$result['goods']);
            foreach ($data as $key=>$val){
                if($val['state']==1){
                    $newdata[$key]['link']='h/init/goodsinfo?id='.$val['produid'];
                    unset($val['state']);
                }else if($val['state']==2){
                     $newdata[$key]['link']='s/init/shopinfo?id='.$val['produid'];
                    unset($val['state']);
                }
                $newdata[$key]['name']=$val['name'];
                $newdata[$key]['img']=$val['img'];
            }
            return $newdata;
        }else{
            return false;
        
        }
    }
    /**
     * 通过用户id获取用户的浏览记录
     */
    function listRecords(){
        //获取用户的商城浏览记录
        $sql='select a.user_id as userid,a.shop_id as shopid,
               a.records_source as source from '.$this->conv_source.' 
               as a left join  '.$this->conv_user.' as b 
               on a.user_id = b.user_id where b.user_status = 1 and 
               a.records_status=1 and b.user_id='.$this->userid.
               ' order by a.records_updatetime desc limit 20';
        $query=$this->db->query($sql);
        $result=$query->result_array();
        if($result){
            return $result;
        }else{
            return  false;
        }
    }
    
}