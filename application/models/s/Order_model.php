<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * 
 * @author xt
 * 订单模块  包含
 *      orderLists();订单列表
 */
class Order_model extends CI_Model{
     //商城订单表
    private     $conv_order   = 'conv_shop_order';
    //商品表
    private     $conv_shop    = 'conv_shop_copy';
    //商户商品表
    private     $conv_busine    = 'conv_shop_busine';
    //地址表
    private     $conv_address = 'conv_user_address';
    //构造函数
    function __construct(){
        parent::__construct();
        $this->load->database();
    }
    /**
     * @abstract 订单模块  用户的个人订单列表
     * @param  int     userid  用户id
     * @param  string  state   订单状态
     * @
     */
   /*  function orderLists(){
        switch ($this->state){
            case 'all':
                $where = '';
                break;
            case 'pay':
                $where = 'order_state = 1 and';
                break;
            case 'pending':
                $where = 'order_state = 2 and';
                break;
            case 'confim':
                $where = 'order_state = 3 and';
                break;
            case 'cancel':
                $where = 'order_state = 4 and';
                break;
            case 'deal':
                $where = 'order_state = 10 and';
                break;
            default :
                $where = '';
                break;
        }
        $page=($this->page-1)*10;
        $sql='select order_number,order_price,order_integral,goods_id,
               order_method,order_state
               from '.$this->conv_order.' where  '.$where.'  user_id='.$this->userid
               .' order by order_jointime desc,order_updatetime desc limit '.$page.',10';
        $query=$this->db->query($sql);
        $data['result']=$query->result_array();
        if(!$data['result']){
            return  false;
        }
        //获取总页数
        $page_sql='select count(order_number) as countPage from '.$this->conv_order.' where  '.$where.'  user_id='.$this->userid
               .' order by order_jointime desc,order_updatetime desc';
        $page_query=$this->db->query($page_sql);
        $pageCount=$page_query->row_array();
        if(!$pageCount){
            return  false;
        }
        //订单状态
        $state=$this->config->item('order_state');
        $stateifno=$this->config->item('order_stateinfo');
        foreach ($data['result'] as $key=>$val){
            $list=$this->goodsListInfo($val['goods_id']);
            $data['result'][$key]['list']=$list['list'];
            $data['result'][$key]['total']=$list['total'];
            $data['result'][$key]['stateinfo']=$stateifno[$val['order_state']]['name'];
            $data['result'][$key]['stateurl']=$stateifno[$val['order_state']]['url'];
            $data['result'][$key]['state']=$state[$val['order_state']];
            if($val['order_method']== 2){
                $data['result'][$key]['price']=$list['pri']/100;
                $data['result'][$key]['integral']=0;
            }else{
                $data['result'][$key]['price']=0;
                $data['result'][$key]['integral']=$list['integral']/100;
            }
        }
        $data['countPage']=ceil($pageCount['countPage']/10);
        return $data;
    }*/
    /**
     * @abstract  订单模块    根据商品ID 查询订单内商品详情
     * @param  string    $id      商品ID
     * @param  string    $method  交易方式
     * @return boolean|multitype:number unknown
     */
    function  goodsListInfo($id){
        $arr=explode(',', $id);
        $goodsid='';
        $goodsinfo='';
        foreach ($arr as $key=>$val){
            $val=trim($val,'[]');
            $val=explode('-', $val);
            $goodsid .= $val['0'].',';
            $goodsinfo[$val['0']]=$val['1'];
        }
        $sql='select shop_id,shop_price,shop_integral,shop_name,shop_img from '.$this->conv_shop.' where shop_id in('.trim($goodsid,',').') ';
        $query=$this->db->query($sql);
        $goodslist=$query->result_array();
        if(!$goodslist){
            return  false;
        }
        $total = 0;
        $pri = 0;
        $integral  = 0;
        foreach ($goodslist as $key=>$val){
            $goodslist[$key]['shop_price']=$val['shop_price']/100;
            $goodslist[$key]['shop_name']=mb_substr($val['shop_name'],0,18,'utf-8').'...';
            $goodslist[$key]['num']=$goodsinfo[$val['shop_id']];
            $total = $total + $goodsinfo[$val['shop_id']];
            $integral = $integral +$val['shop_price'];
            $pri = $pri + $val['shop_integral'];
        }
        return array('list'=>$goodslist,'total'=>$total,'integral'=>$integral,'pri'=>$pri);
    }
    /**
      * @abstract  通过订单编号查找订单信息
      * @param int  number  订单编号
      * @return array | bool
      */
     function orderInfo(){
        $sql='select a.order_number,a.order_price,a.order_integral,a.goods_id,
              a.order_method,a.order_state,a.order_jointime,address_id as addrid
              ,a.order_updatetime as updatetime from '.$this->conv_order.' as a
              where  order_number="'.$this->number.'" and  a.user_id='.
              $this->userid;
        $query=$this->db->query($sql);
        $order=$query->row_array();
        if(!$order){
            return  false;
        }
        
        $state=$this->config->item('order_state');
        $stateifno=$this->config->item('order_stateinfo');
        $order['stateinfo']=$stateifno[$order['order_state']]['name'];
        $order['stateurl']=$stateifno[$order['order_state']]['url'];
        $order['state']=$state[$order['order_state']];
        //根据交易方式显示订单的成交价格
        if($order['order_method']== 2){
            $order['order_price']=$order['order_price']/100;
            $order['order_integral']=0;
        }else if($order['order_method']== 1){
            $order['order_price']=0;
            $order['order_integral']=$order['order_integral']/100;
        }else{
            $order['order_price']=$order['order_price']/100;
            $order['order_integral']=$order['order_integral']/100;
        }
        $shoplist=$this->goodsListInfo($order['goods_id']);
        $goodslist=array_merge($shoplist,$order);
        return $goodslist;
        
     }
     /**
      * @abstract 通过用户userid 获取订单记录
      */
     function getUserOrderList(){
         $sql='select a.goods_id as goodid,a.order_jointime,a.order_updatetime,a.order_number as number
             from '.$this->conv_order.' as a where a.buyuser_id ='.$this->userid;
         $query=$this->db->query($sql);
         $order=$query->result_array();
         if(!$order){
             return  false;
         }
         $goodsid='';
         $regoodid=array();
         $newop=array();
         foreach($order as $key=>$val){
             $regoodid=explode(',',$val['goodid']);
             //var_dump($regoodid).'**';
             foreach ($regoodid as $k=>$v){
                 $regoodid[$k]=trim($regoodid[$k],'[]');
                 $regoodid[$k]=explode('-', $regoodid[$k]);
                 $goodsid .= $regoodid[$k]['0'].',';
                 $newop[$regoodid[$k][0]] =$regoodid[$k][1];
                /*  $newop[$k]=$regoodid[$k][0];
                 $newop[$k]=$regoodid[$k][1];
                 var_dump($newop); */
                 
             }
             //echo $order[$key].'&*';
             $order[$key]['option']=$newop;
         }
         $order['goodsid']=$goodsid; 
         return $order;
     }
    /**
     * @abstract 获取订单列表 读取买入的记录
     */
     function buyOrderList(){
        $result=array();
        $result['order']=$this->getUserOrderList();
        $sql='select a.shop_id,a.busine_price as price,a.busine_integral as integral,
             b.shop_name as name from '.$this->conv_busine.' as a left join '.$this->conv_shop.'
             as b on a.shop_id=b.shop_id  where a.shop_id in('.trim($result['order']['goodsid'],',').') 
             and a.user_id='.$this->userid;
         $query=$this->db->query($sql);
         unset($result['order']['goodsid']);
         $result['goodslist']=$query->result_array();
         if(!$result['goodslist']){
             return  false;
         }
        return $result;
     }
}
