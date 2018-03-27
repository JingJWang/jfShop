<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class  busine_model extends CI_Model{
    //商家产品表
    private     $conv_busine   = 'conv_shop_busine';
    //商品表
    private     $conv_shop    = 'conv_shop_copy';
    
    function __construct(){
        parent::__construct();
        $this->load->database();
    }
    /**
     * 查看所有产品--商户发布的商品
     */
    function goodList(){
        $page='';
        $where='where b.shop_status=1';
        $newData=array();
        if(!empty($this->data)){
            //筛选产品名字
            if(!empty($this->data['name'])){
                $where .= ' and shop_name like "%'.$this->data['name'].'%"';
            }else{
                $where .='';
            }
            //分页
            if(isset($this->data['page']) && !empty($this->data['page']) && $this->data['page']>0){
                $page=$this->data['page']*10;
            }else{
                $page=0;
            }
           $sql='select a.shop_id as shopid,b.shop_name as shopName,a.busine_price as price,
                a.busine_integral as integral,a.busine_num as num,a.busine_status as status
                 from '.$this->conv_busine.' as a left join '.$this->conv_shop.'
                   as b on a.shop_id=b.shop_id '.$where.' limit '.$page.',10';
            $query=$this->db->query($sql);
            $data['result']=$query->result_array();
            //获取总页数
            $res_sql='select count(a.shop_id) as nums from '.$this->conv_busine.' as a 
                    left join '.$this->conv_shop.' as b on a.shop_id=b.shop_id '.$where;
            $res_query=$this->db->query($res_sql);
            $data['res_result']=$res_query->row_array();
            if($data['result'] && $data['res_result']){
                return $data;
            }else{
                return false;
            }
        }else{
            return  false;
        }
    }
    /**
     * @abstract 通过商品id 和用户id 获取商品详情
     */
    function getShopOneBusine(){
        $sql='select * from '.$this->conv_busine.' where
            shop_id='.$this->shopid.' and user_id='.$this->userid;
        $query=$this->db->query($sql);
        $result=$query->row_array();
        if($result){
            return $result;
        }else{
            return  false;
        }
    }
    /**
     * 商户发布商品
     */
    function insert(){
        $result=$this->getShopOneBusine();
        if($result){
            return false;
        }
        $price=0;//钱
        $intergral=0;//金点
        //判断用户选择商品价值的方式
        switch ($this->option['method']){
            case 1://钱
                $price=$this->option['values'];
                break;
            case 2://金点
                $intergral=$this->option['values'];
                break;
            default:break;
        }
        //判断商家是否是自营
        if($this->userid==7){
            $sign=1;
        }else{
            $sign=0;
        }
        $data=array(
            'shop_id'=>$this->shopid,
            'user_id'=>$this->userid,
            'busine_num'=>$this->option['num'],
            'busine_price'=>$price,
            'busine_integral'=>$intergral,
            'busine_status'=>1,
            'busine_sign'=>$sign,
            'busine_jointime'=>time()
        );
        $this->db->insert($this->conv_busine,$data);
        $row=$this->db->affected_rows();
        if($row==1){
            return true;
        }else{
            return false;
        }
    }
    /**
     * @abstract 修改商品状态/商品价格及数量
     */
    function updateBusine(){
        $result=$this->getShopOneBusine();
        if(!$result){
            return false;
        }
        $price=0;//钱
        $intergral=0;//金点
        //修改sql要改变的值
        switch($this->sign){
            case 1://修改商品状态 上架or下架
                $data=array('busine_status'=>$this->status);
                break;
            case 2://修改商品价格及数量
                switch ($this->option['method']){
                    case 1://钱
                        $price=$this->option['values'];
                        break;
                    case 2://金点
                        $intergral=$this->option['values'];
                        break;
                    default:break;
                }
                $data=array(
                    'busine_num'=>$this->option['num'],
                    'busine_price'=>$price,
                    'busine_integral'=>$intergral,
                );
                break;
        }
        //修改sql的条件
        $where=array(
            'user_id'=>$this->userid,
            'shop_id'=>$this->shopid
        );
        $this->db->update($this->conv_busine,$data,$where);
        $rows = $this->db->affected_rows();
        if($rows==1){
            return true;
        }else{
            return false;
        }
    }
    /**
     * @abstract 通过userid 查找该用户发布的所有商品
     */
    function getUserShop(){
        $sql='select * from '.$this->conv_busine.' where
             user_id='.$this->userid;
        $query=$this->db->query($sql);
        $result=$query->result_array();
        if($result){
            return $result;
        }else{
            return  false;
        }
    }
}