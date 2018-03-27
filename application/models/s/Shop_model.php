<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class  shop_model extends CI_Model{
    //商户商品表
    private     $conv_busine   = 'conv_shop_busine';
    //商城订单表
    private     $conv_order   = 'conv_shop_order';
    //商品表
    private     $conv_shop    = 'conv_shop_copy';
    //购物车表
    private     $conv_package = 'conv_shop_package';
    //地址表
    private     $conv_address = 'conv_user_address';
    //热门搜索表
    private     $conv_search ='conv_shop_search';
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
        $sourcejt=array();//用来储存来源字符串处理
        $sourcepj='';//用来拼接来源字符串数组处理后的结果
        $order='';//用来判断是价格是由高到低 还是由低到高
        $page='';
        $pri=array();//用来储存价格区间
        $where='where f.busine_status=1 and a.shop_status=1 and b.brand_status=1 and c.type_status=1';
        $newData=array();
        if(!empty($this->data)){
            //筛选产品名字
            if(!empty($this->data['name'])){
                $where .= ' and shop_name like "%'.$this->data['name'].'%"';
            }else{
                $where .='';
            }
            //按价格高低筛选 默认不筛选
            if(!empty($this->data['price'])){
                switch($this->data['price']){
                    case 'top':
                        $order=' order by a.shop_price asc, a.shop_integral asc ';
                        break;
                    case 'bottom':
                        $order=' order by a.shop_price desc, a.shop_integral desc ';
                        break;
                    case 'center':
                        $order='';
                        break;
                    case '':
                        $order='';
                    default:
                        $order='';
                        break;
                }
            }else{
                $order='';
            }
            //价格区间筛选
            if(isset($this->data['pri']) && !empty($this->data['pri'])){
                $pri=explode('-', $this->data['pri']);
                if(count($pri)==2){
                    $where .= ' and a.shop_integral between '.$pri[0].'*100 and '.$pri[1].'*100';
                }else if(count($pri)==1 && $pri[0]=='100'){
                    $where .= ' and a.shop_integral <='.$pri[0].'*100';
                }else if(count($pri)==1 && $pri[0]>='10000'){
                    $where .= ' and a.shop_integral >='.$pri[0].'*100';
                }
            }else{
                $where .='';
            }
            //来源筛选
            if(!empty($this->data['source'])){
                $sourcejt=explode(',', $this->data['source']);
                foreach($sourcejt as $k=>$v){
                    $sourcepj .='"'.$v.'"'.',';
                }
                $sourcepj=substr($sourcepj, 0,-1);
                $where .= ' and a.shop_source in ('.$sourcepj.')';
            }
            //品牌筛选
            if(!empty($this->data['brand'])){
                $where .= ' and a.brand_id in ('.$this->data['brand'].')';
            }else{
                $where .='';
            }
            //分类筛选
            //先判断大类是否有值 如果大类有值 小类没值  就获取大类名下的所有小类的值,如果大类有值,小类有值 那就直接获取小类的值
            if(!empty($this->data['classbig'])){
                if(!empty($this->data['classsmall'])){
                    $where .= ' and a.type_Id ='.$this->data['classsmall'];
                }else{
                    //获取大类的值
                    //获取小类的值,并把要用的值拼接到一块
                    $type='';
                    $this->load->model('h/Goods_model');
                    $this->Goods_model->id=$this->data['classbig'];
                    $smallResu=$this->Goods_model->getTypeSamllName();
                    foreach($smallResu as $key=>$val){
                        $type .=$val['typeid'].',';
                    }
                    $where .= ' and a.type_Id in('.substr($type,0,strlen($type)-1).')';
                }
            }else{
                $where .='';
            }
            //分页
            if(isset($this->data['page']) && !empty($this->data['page']) && $this->data['page']>0){
                $page=$this->data['page']*20;
            }else{
                $page=0;
            }
           /*  $sql='select a.*,b.brand_id as brandid,b.brand_name as brandname,
                c.type_id as typeid,c.type_name as typename
                 from '.$this->conv_shop.' as a left join '.$this->conv_brand.'
                   as b on a.brand_id=b.brand_id left join conv_type
                   as c on a.type_Id=c.type_id '.$where.$order .' limit '.$page.',10'; */
            $sql='select f.user_id as userid,f.busine_id as busineid,a.*,b.brand_id as brandid,b.brand_name as brandname,
                c.type_id as typeid,c.type_name as typename,f.busine_price as price,f.busine_integral as integral
                 from '.$this->conv_busine.' as f left join '.$this->conv_shop.' as a on f.shop_id=a.shop_id
                 left join '.$this->conv_brand.' as b on a.brand_id=b.brand_id left join conv_type
                 as c on a.type_Id=c.type_id '.$where.$order .' limit '.$page.',20';
            $query=$this->db->query($sql);
            $data['result']=$query->result_array();
            //获取总页数
            /* $res_sql='select count(a.shop_id) as nums from '.$this->conv_shop.' as a left join '.$this->conv_brand.'
                   as b on a.brand_id=b.brand_id left join conv_type
                   as c on a.type_Id=c.type_id '.$where.$order; */
            $res_sql='select count(a.shop_id) as nums from '.$this->conv_busine.' 
                   as f left join '.$this->conv_shop.' as a on f.shop_id=a.shop_id 
                   left join '.$this->conv_brand.' as b on a.brand_id=b.brand_id 
                   left join conv_type as c on a.type_Id=c.type_id '.$where.$order;
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
     * @abstract 获取用户要发布的商品信息
     */
    function getReleaseShop(){
        $where='';
        //筛选产品名字
        if(!empty($this->data['name'])){
            $where .= ' and shop_name like "%'.$this->data['name'].'%"';
        }else{
            $where .='';
        }
        //分页
        if(isset($this->data['page']) && !empty($this->data['page']) && $this->data['page']>0){
            $page=$this->data['page']*20;
        }else{
            $page=0;
        }
        $sql='select a.shop_id as shopid,a.shop_name as shopname 
            from '.$this->conv_shop.' as a where a.shop_status=1'.$where .' limit '.$page.',20';
        $query=$this->db->query($sql);
        $data['result']=$query->result_array();
        $res_sql='select count(a.shop_id) as nums from '.
        $this->conv_shop.' as a where a.shop_status=1'.$where;
        $res_query=$this->db->query($res_sql);
        $data['res_result']=$res_query->row_array();
         
        if($data['result'] && $data['res_result']){
            return $data;
        }else{
            return false;
        }
    }
    /**
     * @abstract 关键词添加
     */
    function insert(){
        $data=array(
            'search_name'=>$this->name,
            'user_id'=>$this->userid,
            'search_searchtimes'=>1,
            'search_status'=>1,
            'search_jointime'=>time()
        );
        $this->db->insert($this->conv_search,$data);
        $row=$this->db->affected_rows();
        if($row==1){
            return true;
        }else{
            return false;
        }
    }
    /**
     * @abstract 修改关键词
     */
    function updateSearch(){
        $sql='update '.$this->conv_search .' set search_searchtimes=search_searchtimes+1,
            search_updatetime='.time().' where user_id='.$this->userid.' and '.$this->where;
        $query=$this->db->query($sql);
        $rows = $this->db->affected_rows();
        if($rows==1){
            return true;
        }else{
            return false;
        }
    }
    /**
     * @abstract 通过名字搜索查找关键词是否存在
     */
    function selectSearch(){
        $sql='select * from '.$this->conv_search.
        ' where search_status=1 and user_id='.$this->userid.' and '.$this->where;
        $query=$this->db->query($sql);
        $result=$query->result_array();
        return $result;
    }
    /**
     * @abstract 判断热门搜索是否有值
     */
    function findSearch(){
        if($this->name!=''){
            $this->where ='search_name="'.$this->name.'"';
        }else{
            $this->where ='1=1';
        }
        $result=$this->selectSearch();
        if($result){
            //搜索的关键词存在表里面 修改表里面关键词的搜索次数
            return $this->updateSearch();
             
        }else{
            //搜索的关键词不存在表里面 添加关键词到表里面
            return $this->insert();
        }
    }
    /**
     * @abstract 删除关键词
     */
    function delSearch(){
        $sql='delete from '.$this->conv_search.' where user_id='.$this->userid;
        $query=$this->db->query($sql);
        $rows = $this->db->affected_rows();
        if($rows>=1){
            return true;
        }else{
            return false;
        }
    }
    /**
     * @abstract 查找所有关键词
     */
    function selectSearchList(){
        $where='';
        if($this->usersid!=''){
            $where ='and user_id='.$this->usersid;
        }
        $sql='select search_name as hosName from '.$this->conv_search.' where search_status=1 '.$where;
        $jintime=' order by search_jointime desc,search_updatetime desc limit 8';
        $hottime=' order by search_searchtimes desc limit 8 ';
        //热门搜索
        $query=$this->db->query($sql.$hottime);
        //$result['hot']=$query->result_array();
        $result['hot']=$this->config->item('hot_search');
        //最近搜索
        $jinquery=$this->db->query($sql.$jintime);
        $result['jintime']=$jinquery->result_array();
        return $result;
    }
    /**
     * @abstract 通过商品id获取原始商品表里面的价格
     */
    function getshopInfo(){
        $sql='select a.shop_id as shopid,a.shop_price as price,a.shop_integral as integral 
            from  '.$this->conv_shop.' as a where a.shop_status=1 and a.shop_id='.$this->id;
        $query=$this->db->query($sql);
        $result=$query->row_array();
        if($result){
            return $result;
        }else{
            return  false;
        }
    }
    /**
     * @abstract 根据商品id 读取商品详情
     * @param  id  int  商品 ID
     * @return array 查询到结果集返回   否则返回  false
     */
    function goodInfo(){
        $sql='select b.busine_id as busineid,b.user_id as selluserid, a.shop_id as shopid,b.busine_price as price,
        b.busine_integral as integral,a.shop_name as shopname,a.shop_img as img,
        a.shop_source as source,a.shop_content as content,a.shop_picture as picture,
        a.shop_source_info as sourceinfo,a.shop_chain as chain 
        from '.$this->conv_busine.' as b left join '.$this->conv_shop.' as a 
         on b.shop_id=a.shop_id where b.busine_id='.$this->busineid;
        $query=$this->db->query($sql);
        $result=$query->result_array();
        if($result){
            return $result;
        }else{
            return  false;
        }
    }
    /**
     * @abstract 根据购物车物品的ID 读取商品详情
     * @param  id  int  商品 ID
     * @return array 查询到结果集返回   否则返回  false
     */
    function shopDuoInfo(){
        $sql='select c.busine_id as busineid,c.shop_id as shopid,c.busine_price as price,
            c.busine_integral as integral,a.shop_name as shopname,
            a.shop_img as img,a.shop_source as source,b.package_id as packageid,
            a.shop_source_info as sourceinfo,b.order_num as num
            from '.$this->conv_busine .' as c left join '.$this->conv_shop.' as a 
            on c.shop_id=a.shop_id 
            right join  '.$this->conv_package.' as b
            on a.shop_id=b.goods_id where b.order_status=1 
            and b.package_id in ('.$this->id.') and b.user_id='.$this->userid;
        $query=$this->db->query($sql);
        $result=$query->result_array();
        if($result){
            return $result;
        }else{
            return  false;
        }
    }
    /**
     * @abstract  提交订单  保存订单内容
     * @return  bool 提交订单成功返回  true 失败返回 false
     */
    function insertOrder(){
         $this->orderNum=$this->ordrenumber();//获取订单编号
         $shop=$this->shopinfo;//商品内容
         $data_order=array(
                     'buyuser_id'=>$this->userid,//用户ID
                     'busine_id'=>$shop['busineid'],//购买的商品在商户商品里面的编号
                     'order_number'=>$this->orderNum,//订单编号
                     'goods_id'=>$shop['shpid'],//商品ID-购买数量
                     'order_type'=>$shop['type'],//订单类型   1 单商品  2 多商品 
                     'order_method'=>$shop['pay'],//订单交易方式 1 积分 2 金钱 3综合
                     'order_price'=>$shop['pri'],//订单总价
                     'order_integral'=>$shop['inte'],//订单总积分
                     'address_id'=>$this->addrId,//用户收货地址
                     'order_jointime'=>time(),
                     'order_state'=>1,
                     'order_status'=>1
         );
         //根据提交的订单类型 处理一个订单内多个商品
         if($shop['many'] != 1){
             $query=$this->db->insert($this->conv_order,$data_order);
             $row=$this->db->affected_rows();
             if($query == false  ||$row != 1){
                 return false;
             }else{
                 return true;
             }
         }else{
             //删除购物车提交的商品
             $this->db->trans_start();
             $query=$this->db->insert($this->conv_order,$data_order);
             $row=$this->db->affected_rows();
             $this->row=$shop['row'];
             $del=$this->delCarShop();
             $this->db->trans_complete();
             if ($this->db->trans_status() === false || $del == false){
                return false;
             }else{
                return true;
             }
         }
        
    }
    /**
     * @abstract  根据购物车内商品的ID 删除商品
     * @param  string  购车内商品的ID
     * @return 执行成功返回  true 否则返回false
     */
    function  delCarShop(){
        //如果是多商品 就需要把多商品 从购物车里面删除
        $sql='update '.$this->conv_package.' a set a.order_status=-1, 
              order_updatetime='.time().' where a.user_id='.
              $this->userid.' and a.package_id 
              in('.$this->packageid.') and a.order_status=1';
        $query=$this->db->query($sql);
        $rows = $this->db->affected_rows();
        if($query == false || $rows != $this->row){
            return false;
        }else{
            return true;
        }
    }
    
    /**
     * 根据订单编号查询订单
     */
    function orderDetail(){
        $sql='select * from '.$this->conv_order.' where order_status=1 
            and order_state =1 and order_number='.$this->orderNum.' and
            buyuser_id='.$this->userid;
         $query=$this->db->query($sql);
         $result=$query->row_array();
         if($result){
             return $result;
         }else{
             return false;
         }
     }
     /**
      * @abstract 订单支付- 根据订单编号 查找该订单详细信息 如果找到该订单  支付成功后就更改该订单的支付状态,没有就不更改该订单状态
      */
     function selectOrder(){
         $sql='select * from '.$this->conv_order.' where order_status=1 
             and order_state =1 and order_number='.$this->orderId.' and 
             buyuser_id='.$this->userid;
         $query=$this->db->query($sql);
         $result=$query->row_array();
         $datas=array(
             'order_number'=>$result['order_number'],
             'buyuser_id'=>$result['buyuser_id']
         );
         if($result){
            $this->db->update($this->conv_order,array('order_state'=>2),$datas);
	        $row=$this->db->affected_rows();
	        if($row == 1){
	           return $result['order_number'];
	        }else{
	           return  false;
	        } 
         }else{
             return  false;
         }
     }
    /**
     * 查看收货地址
     */
     function addressList(){
     	$sql='select * from '.$this->conv_address.' a where a.address_status>0 and a.user_id='.$this->userid
     	.' order by a.address_status desc';
     	$query=$this->db->query($sql);
        $result=$query->result_array();
        if($result){
            return $result;
        }else{
            return  false;
        }
     }
     /**
      * @abstract 地址管理  读取用户默认收货地址
      * @param  userid  int  用户ID
      * @return  读取成功 返回array 失败返回 bool false
      */
      function selectDefAdd(){
      	$sql='select  address_id,address_name,address_phone,address_county,
      	      address_province,address_city,address_area,address_details,
      	      address_jointime  from '.$this->conv_address.' 
      	      where address_status ='.$this->state.' and user_id='.$this->userid;
     	$query=$this->db->query($sql);
        $result=$query->row_array();
        if($result){
            return $result;
        }else{
            return  false;
        }
    }  
    /**
     * 添加地址 
     */
     function addAddress(){
     	$data=array(
     		'user_id' =>$this->userid,
     		'address_name' =>$this->name,
			'address_phone'=>$this->phone,
			'address_province'=>$this->province,
			'address_city'=>$this->city,
			'address_area'=>$this->area,
			'address_details'=>$this->details,
			'address_jointime'=>time()
     	);
     	$this->db->insert($this->conv_address,$data);
     	$row=$this->db->affected_rows();
        if($row == 1){
            return true;
        }
        return  false;
     }
     /**
      * @abstract  通过用户id 查看购物车详细内容
      * @param   userid  int 用户id
      * @return  读取成功返回结果集  array 失败返回bool false
      */
      function selectCart(){
      	$sql='select b.shop_img as img ,a.package_id  as packId,b.shop_name as name,
      	      c.busine_price as price,c.busine_integral as integral,c.shop_id as shopid,
      	      a.order_num as num,b.shop_source as sources,b.shop_source_info as source  from '.
      	      $this->conv_package.' as a left join '.
      	      $this->conv_busine.' as c on a.goods_id=c.shop_id left join '.
      	      $this->conv_shop.' as b on a.goods_id=b.shop_id 
      	      where order_status=1 and a.user_id='.$this->userid;
          	$query=$this->db->query($sql);
            $result=$query->result_array();
            if($result){
            	foreach($result as $key=>$val){
            		$result[$key]['source']=json_decode($val['source']);
            	}
                return $result;
            }else{
                return false;
            }
      }
      /**
      * @abstract 通过商品id和商户商品id 查询购物车中是否存在相同的商品
      * 如果存在则修改数量 不存在则添加商品到购物车
      * @param  shopid  int 商品ID
      * @param  num int 商品数量
      * @return  bool  存在商品 数量修改成功返回true 否则返回false
      */
      function selectShopIdCart(){
          //通过商品id 查找购物车里面是否存在该商品 ,如果存在就修改该购物车地址
            $sql='select * from '.$this->conv_package.' where busine_id='.
                  $this->busineid.' and goods_id='.$this->shopid.' and 
                   user_id='.$this->userid.' and order_status = 1';
            $query=$this->db->query($sql);
            $result=$query->result_array();
            if($result){
                $sql='update '.$this->conv_package.' set order_num=order_num+'.
                    $this->num.' where busine_id='.$this->busineid.' and goods_id='.$this->shopid.
                    ' and user_id='.$this->userid.' and order_status = 1';
                $this->db->query($sql);
                $row=$this->db->affected_rows();
                if($row == 1){
                    return true;
                }
                return  false;
            }else{
                return false;
            }
      }
     /**
      * @abstract  查看购物车商品个数
      * @param  userid  int  用户id
      * @return array 获取成功返回结果集  获取失败返回 bool false
      */
      function selectCartNum(){
      	$sql='select sum(order_num) as num from '.$this->conv_package.'
      		 where order_status=1 and user_id='.$this->userid;
      	$query=$this->db->query($sql);
        $result=$query->row_array();
        if($result){
            return $result;
        }else{
            return false;
        }
      }
      /**
       * @abstract  添加商品到购物车 先查询购物车是否存在该商品 如果存在 就修改个数 如果不存在就添加
       * @param userid  int   用户id
       * @param shopid  int   商品id
       * @param num   int  商品数量
       * @return bool  添加到购物车成功返回true 失败返回false
       */
       function insertCart(){
           //$sel=$this->selectCartNum();
    	   //修改购物车产品的个数
    	   $res=$this->selectShopIdCart();
    	   if($res){
    	       return true;
    	   }else{
    	       $data=array(
    	           'user_id'=>$this->userid,
    	           'goods_id'=>$this->shopid,
    	           'busine_id'=>$this->busineid,
    	           'order_num'=>$this->num,
    	           'order_jointime'=>time()
    	       );
    	       $this->db->insert($this->conv_package,$data);
    	       $row=$this->db->affected_rows();
    	       if($row == 1){
    	           return true;
    	       }else{
    	           return  false;
    	       }   
    	   }
       }
       /**
        * @abstract 根据购车内物品ID 查询商品详细信息
        * @param  strIng id  商品ID
        * @return  获取成功返回array 失败返回bool false
        */
       function selGoods(){
           $sql='select  a.order_num as ordernum,b.busine_id as busineid,b.shop_id as shopid,
                 b.busine_integral as integral,b.busine_price as price 
                 from  '.$this->conv_package.'  as a right join '.$this->conv_busine.' as b  
                 on a.busine_id= b.busine_id  where a.user_id = '.$this->userid.
                ' and a.package_id in('.$this->packid.') and b.busine_id in ('.$this->busineid.')';
           $reult=$this->db->query($sql);
           if($reult){
               $data=$reult->result_array();
    	        return $data;
    	   }else{
    	        return  false;       		
           }
       }
       
      /**
        * 删除购物车商品
        */
       function delCart(){
         $sql='update '.$this->conv_package.' a set a.order_status =-1 where a.package_id in ('.$this->id.')';
         $this->db->query($sql);
         $row=$this->db->affected_rows();
         if($row >0){
            return true;
         }
         return  false;
      }
      /**
       * 修改购物车物品
       */
      function updateCart(){
          $sql='update '.$this->conv_package.' a set a.order_num =
            '.$this->resul['num'].',a.order_updatetime='.time().' where a.user_id='.$this->userid
                  .' and a.package_id ='.$this->resul['packid'].' and a.order_status=1';
          $this->db->query($sql);
          $row=$this->db->affected_rows();
          if($row > 0){
              return true;
          }
          return  false;
      }
    /**
     *@abstract 生成订单订单编号
     * @return string
     */
    function ordrenumber(){
        return date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8).rand(1000,9999);
    }
}