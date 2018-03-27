<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class  shop_model extends CI_Model{
    //商城订单表
    private     $conv_shop   = 'conv_shop_copy';
    //分类表
    private     $conv_type ='conv_type';
    //品牌表
    private     $conv_brand ='conv_brand';
    
    function __construct(){
        parent::__construct();
        $this->load->database();
    }
    /**
     * 查看所有产品
     */
    function goodList(){
        $sourcepj='';//用来拼接来源字符串数组处理后的结果
        $order='';//用来判断是价格是由高到低 还是由低到高
        $page='';
        $pri=array();//用来储存价格区间
        $where='where a.shop_status=1 and b.brand_status=1 and c.type_status=1';
        $newData=array();
        if(!empty($this->data)){
           
            //产品展示筛选
            if(!empty($this->data['kind'])){
                $where .=' and shop_index="'.$this->data['kind'].'"';
                
            }else{
                $where .=' and shop_index="good"';
            }
            $order=' order by shop_index_val desc';
            //分页
            if(isset($this->data['page']) && !empty($this->data['page']) && $this->data['page']>0){
                $page=$this->data['page']*10;
            }else{
                $page=0;
            }
            $sql='select a.*,b.brand_id as brandid,b.brand_name as brandname,
                c.type_id as typeid,c.type_name as typename
                 from '.$this->conv_shop.' as a left join '.$this->conv_brand.'
                   as b on a.brand_id=b.brand_id left join '.$this->conv_type.'
                   as c on a.type_Id=c.type_id '.$where.$order .' limit '.$page.',10';
            $query=$this->db->query($sql);
            $data['result']=$query->result_array();
            //获取总页数
            $res_sql='select count(a.shop_id) as nums from '.$this->conv_shop.' as a left join '.$this->conv_brand.'
                   as b on a.brand_id=b.brand_id left join '.$this->conv_type.'
                   as c on a.type_Id=c.type_id '.$where.$order;
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
}