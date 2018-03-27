<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class  Goods_model extends CI_Model{
    //订单回收表
    private     $conv_order ='conv_order';
    //商品表
    private     $conv_goods ='conv_goods_copy';
    //回收车表
    private     $conv_package ='conv_package';
    //分类表
    private     $conv_type ='conv_type';
    //品牌表
    private     $conv_brand ='conv_brand';
    //热门搜索表
    private     $conv_search ='conv_hot_search';
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
        $where='where a.goods_status=1 and b.brand_status=1 and c.type_status=1';
        $newData=array();
        if(!empty($this->data)){
            //筛选产品名字
            if(!empty($this->data['name'])){
                $where .= ' and goods_name like "%'.$this->data['name'].'%"';
            }else{
                $where .='';
            }
            //按价格高低筛选 默认不筛选
            if(!empty($this->data['price'])){
            	switch($this->data['price']){
	                case 'top':
	                    $order=' order by a.goods_price asc, a.goods_integral asc ';
	                    break;
	                case 'bottom':
	                    $order=' order by a.goods_price desc, a.goods_integral desc ';
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
                    $where .= ' and a.goods_price between '.$pri[0].'*100 and '.$pri[1].
                              '*100 ';
                    // and a.goods_integral between '.$pri[0].'*100 and '.$pri[1].'*100
                }else if(count($pri)==1 && $pri[0]=='100'){
                    $where .= ' and a.goods_price <='.$pri[0].
                    '*100 ';
                    //and a.goods_integral <='.$pri[0].'*100
                }else if(count($pri)==1 && $pri[0]=='10000'){
                    $where .= ' and a.goods_price >='.$pri[0].
                    '*100 ';
                    //and a.goods_integral >='.$pri[0].'*100
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
                $where .= ' and a.goods_source in ('.$sourcepj.')';
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
                    $this->id=$this->data['classbig'];
                    //获取小类的值,并把要用的值拼接到一块
                    $type='';
                    $smallResu=$this->getTypeSamllName();
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
                $page=$this->data['page']*10;
            }else{
                $page=0;
            }
           $sql='select a.*,b.brand_id as brandid,b.brand_name as brandname,
                c.type_id as typeid,c.type_name as typename
                 from '.$this->conv_goods.' as a left join '.$this->conv_brand.'
                   as b on a.brand_id=b.brand_id left join conv_type
                   as c on a.type_Id=c.type_id '.$where.$order .' limit '.$page.',10';
            $query=$this->db->query($sql);
            $data['result']=$query->result_array();
            //获取总页数
            $res_sql='select count(a.goods_id) as nums from '.$this->conv_goods.' as a left join '.$this->conv_brand.'
                   as b on a.brand_id=b.brand_id left join conv_type
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
    /**
     * 通过id 查看商品详情
     */
    function goodInfo(){
        $sql='select a.goods_id as goodsid,a.goods_price as price,a.goods_picture as picture,
            a.goods_name as goodsname,a.goods_img as img,a.goods_source as source,
            a.goods_source_info as sourceinfo,a.goods_integral as integral,
            a.goods_content as content,a.goods_chain as chain
            from '.$this->conv_goods.' as a  where goods_id ='.$this->id;
        $query=$this->db->query($sql);
        $result=$query->row_array();
        if($result){
            return $result;
        }else{
            return  false;
        }
    }
    /**
     * 通过id 查看多个商品详情
     */
    function goodDuoInfo(){
        $sql='select a.goods_id as goodsid,a.goods_price as price,
            a.goods_integral as integral,a.goods_name as goodsname,
            a.goods_img as img,a.goods_source as source,a.goods_chain as chain,
            b.package_id as packageid,a.goods_source_info as sourceinfo,
            b.order_method as method,b.order_num as num from '.$this->conv_goods
            .' as a right join  '.$this->conv_package.' as b on a.goods_id=
            b.goods_id where b.order_status=1 and 
            b.package_id in ('.$this->id.') and b.user_id='.$this->userid;
        $query=$this->db->query($sql);
        $result=$query->result_array();
        if($result){
            return $result;
        }else{
            return  false;
        }
    }
    /**
     * 关键词添加
     */
    function insert(){
        $data=array(
            'hot_name'=>$this->name,
            'user_id'=>$this->userid,
            'hot_searchtimes'=>1,
            'hot_status'=>1,
            'hot_jointime'=>time()
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
     * 修改关键词
     */
    function updateSearch(){
        $sql='update '.$this->conv_search .' set hot_searchtimes=hot_searchtimes+1,
            hot_updatetime='.time().' where user_id='.$this->userid.' and '.$this->where;
        $query=$this->db->query($sql);
        $rows = $this->db->affected_rows();
        if($rows==1){
            return true;
        }else{
            return false;
        }
    }
    /**
     * 通过名字搜索查找关键词是否存在
     */
    function selectSearch(){
       $sql='select * from '.$this->conv_search.
        ' where hot_status=1 and user_id='.$this->userid.' and '.$this->where;
        $query=$this->db->query($sql);
        $result=$query->result_array();
        return $result;
    }
    /**
     * 判断热门搜索是否有值
     */
    function findSearch(){
        if($this->name!=''){
             $this->where ='hot_name="'.$this->name.'"';
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
     * 删除关键词
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
     * 查找所有关键词
     */
    function selectSearchList(){
        $where='';
        if($this->userid!=''){
            $where ='and user_id='.$this->userid;
        }
        $sql='select hot_name as hosName from '.$this->conv_search.' where hot_status=1 '.$where;
        $jintime=' order by hot_jointime desc,hot_jointime desc limit 8';
        $hottime=' order by hot_searchtimes desc limit 8 ';
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
     * 获取商品类别 大类
     */
    function getTypeName(){
        $sql='select a.type_id as typeid,a.type_name as typename 
            from conv_type as a where a.type_status=1 and a.type_fid=0';
        $query=$this->db->query($sql);
        $result=$query->result_array();
        if($result){
            return $result;
        }else{
            return  false;
        }
    }
    /**
     * 获取商品类别 小类
     */
    function getTypeSamllName(){
        $sql='SELECT a.type_id as typeid,a.type_name as typename,a.type_img as img,a.type_fid as fid 
             FROM `conv_type` a left join conv_type as b  on a.type_fid=b.type_id 
            where a.type_status=1 and b.type_status=1 and a.type_fid='.$this->id;
        $query=$this->db->query($sql);
        $result=$query->result_array();
        if($result){
            return $result;
        }else{
            return  false;
        }
    }
    /**
     * 获取商品品牌
     */
    function getGoodClass(){
        $sql='select a.brand_id as brandid,a.type_id as typeid,a.brand_name as brandname from conv_brand as a 
                where a.brand_status=1 and a.type_id='.$this->id;
        $query=$this->db->query($sql);
        $result=$query->result_array();
        if($result){
            return $result;
        }else{
            return  false;
        }
    }
    /**
     * 搜索商品 
     */
    function search(){
        $sql='select * from '.$this->conv_goods.' as a  where goods_name like "%'.$this->goodsname.'%"';
        $query=$this->db->query($sql);
        $result=$query->result_array();
        if($result){
            return $result;
        }else{
            return  false;
        }
    }
    /**
     * 生产订单记录
     */
    function insertOrder(){
    	$price=0;
    	$intergral=0;
    	$id='';
    	$packid='';
    	$i=0;
    	foreach($this->new_data as $Ke=>$va){
    		$i++;
    		$this->id=$va['id'];
    		$goodsinfo=$this->goodInfo();
    		if($va['method'] ==2){
    		    $price +=$goodsinfo['price']*$va['num'];
    		}else if($va['method'] ==1 ){
    		    $intergral +=$goodsinfo['integral']*$va['num'];
    		}
    		$id =$id.'['.$va['id'].'-'.$va['method'].'-'.$va['num'].'],';
    		if($va['packid']>0){
    		    $packid=$packid.$va['packid'].',';
    		}else{
    		    $packid=0;
    		}
    	}
    	if($i==1){
    		$order_type=1;//单商品
    		if($price<=0 && $intergral>0){
    			$order_method=1;//金点
    		}else if($price>0 && $intergral<=0){
    			$order_method=2;//钱
    		}
    	}else if($i>1){
    		$order_type=2;
    		if($price<=0 && $intergral>0){
    			$order_method=1;//金点
    		}else if($price>0 && $intergral<=0){
    			$order_method=2;//钱
    		}else if($price>0 &&$intergral>0){
    			$order_method=3;
    		}
    	}
        $orderNum=$this->ordrenumber();
        $data_order=array(
            'user_id'=>$this->userid,
            'order_number'=>$orderNum,
            'goods_id'=>$id,
            'order_type'=>$order_type,
            'order_method'=>$order_method,
            'order_integral'=>$intergral,
            'order_price'=>$price,
            'order_name'=>$this->data_order['name'],
            'order_phone'=>$this->data_order['phone'],
            'order_jointime'=>time(),
            'order_state'=>1,
            'order_status'=>1
        );
        $this->db->insert($this->conv_order,$data_order);
        $row=$this->db->affected_rows();
        if($row != 1){
            return false;
        }else{
            if($packid!=0){
                //如果是多商品 就需要把多商品 从购物车里面删除
                $packageid=substr($packid,0,strlen($packid)-1);
                $sel_sql='select count(package_id) as countid from '.$this->conv_package.' where order_status=1' .
                    ' and package_id in ('.$packageid.') and user_id='.$this->userid;
                $sel_query=$this->db->query($sel_sql);
                $sel_row=$sel_query->num_rows();
                if($sel_row>0){
                    $data=$sel_query->row_array();
                }else{
                    return false;
                }
                $sql='update '.$this->conv_package.' a set a.order_status=0 where a.user_id='.$this->userid.
                ' and a.package_id in('.$packageid.')';
                $query=$this->db->query($sql);
                $rows = $this->db->affected_rows();
                if($data['countid']==$rows){
                    return $orderNum;
                }else{
                    return false;
                }
            }else{
                return $orderNum;
            }
        }
    }
    /**
     * 通过用户id及订单编号查询用户订单
     */
    function orderUser(){
        $sql='select * from '.$this->conv_order.'
            where order_status=1 and user_id= '.$this->userid.' and order_number='.$this->orderNum;
        $query=$this->db->query($sql);
        $result=$query->row_array();
        return $result;
    }
    /**
     * 根据订单编号查询订单
     */
    function orderDetail(){
        $conDa=array();
        $conRe=array();
        $result=array();
        $resuData=array();
        $resuKey=array();
        $result=$this->orderUser();
        if($result){
            $conDa=explode(',', substr($result['goods_id'], 0,strlen($result['goods_id'])-1));
            foreach($conDa as $key=>$val){
                $conRe=explode('-',trim($val,'[]'));
                $this->id=$conRe[0];
                $method=$conRe[1];
                $num=$conRe[2];
                $goodsResult=$this->goodInfo();
                $resuKey[$key]['goods_name']=$goodsResult['goodsname'];
                $resuKey[$key]['goods_img']=$goodsResult['img'];
                $resuKey[$key]['chain']=$goodsResult['chain'];
                $resuKey[$key]['order_price']=$goodsResult['price'];
                $resuKey[$key]['order_integral']=$goodsResult['integral'];
                $resuKey[$key]['order_source']=$goodsResult['source'];
                $resuKey[$key]['order_sourceinfo']=json_decode($goodsResult['sourceinfo']);
                $resuKey[$key]['order_type']=$result['order_type'];
                $resuKey[$key]['order_state']=$result['order_state'];
                $resuKey[$key]['order_method']=$method;
                $resuKey[$key]['num']=$num;
            }
            $resuData['order']['order_name']=$result['order_name'];
            $resuData['order']['order_phone']=$result['order_phone'];
            $resuData['order']['order_number']=$result['order_number'];
            $resuData['order']['jointime']=$result['order_jointime'];
            $resuData['order']['order_state']=$result['order_state'];
            $resuData['order']['kdname']=$result['order_kdname'];
            $resuData['order']['kdnumber']=$result['order_kdnumber'];
            $resuData['result']=$resuKey;
            return $resuData;
        }else{
            return  false;
        }
    }
    /**
     * 根据订单编号修改物流信息
     */
    function upOrderTransport(){
        $datas=array(
            'order_kdname'=>$this->data['kdname'],
            'order_kdnumber'=>$this->data['kdNum'],
            'order_state'=>3,
            'order_updatetime'=>time()
        );
        $where=array(
            'order_number'=>$this->data['orderNum'],
            'user_id'=>$this->userid
        );
        $query=$this->db->update($this->conv_order,$datas,$where);
        $num=$this->db->affected_rows();
        if ($query && ($num == 1)){
            return $this->data['orderNum'];
        }else{
            return false;
        }
    }
    /**
     * 通过订单编号及用户id 取消订单
     */
    function cancelOrder(){
        //通过该订单编号及用户id 查询该用户订单是否存在
        $this->orderNum=$this->orderid;
        $result=$this->orderUser();
        if(!$result){
            return false;
        }
        $data=array(
            'order_number'=>$this->orderid,
            'user_id'=>$this->userid,
            'order_status'=>1
        );
        $this->db->update($this->conv_order,array('order_state'=>4),$data);
        $row=$this->db->affected_rows();
        if($row != 1){
            return false;
        }else{
            return true;
        }    
    }
    /**
     * 查看购物车是否有相同的产品及选择收钱的方式 2者是否一致
     */
    function selectCart(){
        $sql='select * from '.$this->conv_package.' a where user_id='.$this->userid.
            ' and goods_id='.$this->id.' and order_method='.$this->method.
            ' and order_status=1';
        $query=$this->db->query($sql);
        $result=$query->row_array();
        if($result){
           $upsql='update '.$this->conv_package.' set order_num=order_num+'.$this->num.' where 
                    user_id='.$this->userid.' and goods_id='.$this->id.
                    ' and order_method='.$this->method.' and order_status=1';
            $this->db->query($upsql);
            $row=$this->db->affected_rows();
            if($row == 1){
                return true;
            }
            return  false;            
        }else{
            return  false;
        }
    }
    /**
     * 生成回收车记录
     */
    function insertPackage(){
        //检查传过来的id 在回收车是否有记录
        $res=$this->selectCart();
        if($res){
            return $this->userid;
        }else{
            $orderNum=$this->ordrenumber();
            $data_order=array(
                'user_id'=>$this->userid,
                'goods_id'=>$this->id,
                'order_method'=>$this->method,
                'order_num'=>$this->num,
                'order_jointime'=>time(),
                'order_status'=>1
            );
            $this->db->insert($this->conv_package,$data_order);
            $row=$this->db->affected_rows();
            if($row != 1){
                return false;
            }else{
                return $this->userid;
            }            
        }
    }
    /**
     * 删除回收车物品
     */
    function delCart(){
        $sql='update '.$this->conv_package.' a set a.order_status =-1 where a.user_id='.$this->userid
              .' and a.package_id in ('.$this->id.')';
        $this->db->query($sql);
        $row=$this->db->affected_rows();
        if($row > 0){
            return true;
        }
        return  false;
    }
    /**
     * 修改回收车物品
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
     * 查看购物车
     */
    function listCart(){
        $sql='select a.package_id as packageid,a.goods_id as goodsId,b.goods_name as goodsName,a.order_method as method,
            b.goods_price as price,b.goods_integral as integral,b.goods_img as img,
            b.goods_source as source,b.goods_source_info as sourceInfo,a.order_num as num
            from '.$this->conv_package.' a left join '.$this->conv_goods.' as b 
            on a.goods_id=b.goods_id where a.order_status=1 and
            a.user_id='.$this->userid;
        $query=$this->db->query($sql);
        $result=$query->result_array();
        if($result){
            foreach($result as $key=>$val){
                $result[$key]['sourceInfo']=json_decode($val['sourceInfo']);
            }
            return $result;
        }else{
            return  false;
        }
    }
    /**
     * 我的订单信息
     * $res=Often::temp($temp);来源于 Often.php文件
     * 
     */
    function orderLists(){
      $this->state==5 ? $where='' : $where=' and order_state = '.$this->state ;
      $this->page==0 ? $page=0: $page=$this->page*10;
      $sql='select goods_id,order_number as number,order_state as state,order_type as type,
            order_method as method,order_integral as integral,order_price as price
            from '.$this->conv_order.' where order_status =1 and user_id= 
            '.$this->userid .$where.' order by order_jointime desc limit '.$page.',10';
        $query=$this->db->query($sql);
        $data['result']=$query->result_array();
        //获取总页数
        $count_sql='select count(order_id) as nums from '.$this->conv_order.' where order_status =1 and user_id=
            '.$this->userid .$where.' order by order_jointime desc';
        $count_query=$this->db->query($count_sql);
        $data['nums']=$count_query->row_array();
        
        $temp=array();
        foreach($data['result'] as $key=>$val){
           $temp=explode(',',trim($val['goods_id'],','));
           //将订单里面的产品id和选择产品的价格方式存入数组中
           $res=Often::temp($temp);
           $option=$this->selgoods($res);
           $data['result'][$key]['num']=$res['num'];
           $data['result'][$key]['res']=$option;
        }
        return $data;
    }
   
    /**
     * 查询订单内商品的内容 
     * $option['id'] 需要查询商品的ID
     * $option['temptype']  商品ID  交易方式集合
     * $option['num']   商品的数量
     * 
     */
    function selgoods($option){
       $sql='select goods_id as goodsid, goods_img as img,goods_name as goodsname,
                goods_price as price,goods_integral as integral, goods_source as source,
                goods_source_info as sourceinfo from '.$this->conv_goods.'
                where goods_status=1 and goods_id in('.$option['id'].')';
       $query=$this->db->query($sql);
       $result=$query->result_array();
       if($result){
            $temparr=array();
            //查询商品内容 重新组合格式  为 商品ID为Key 内容为value二维数组
            foreach ($result as $k=>$n){
                $result[$k]['sourceinfos']=json_decode($n['sourceinfo']);
                $temparr[$n['goodsid']]=$result[$k];
            }
            $goodsarr=array();
            //根据商品ID 交易方式集合 重组结果集
            foreach ($option['temptype'] as $k=>$n){
                $temparr[$n['0']]['method']=$n['1'];
                $temparr[$n['0']]['num']=$n['2'];
                $goodsarr[]=$temparr[$n['0']];
            }
            return $goodsarr;
        }else{
            return false;
        }
    }
    /**
     * 获取物流信息
     */
    function expressRecord(){
        $data=array(
            array('0'=>'1512432491','1'=>'仓库已接单'),
            array('0'=>'1512521791','1'=>'订单已经从天津物流中心发出'),
            array('0'=>'1512605191','1'=>'订单进入河北物流中心发出'),
            array('0'=>'1512691791','1'=>'订单到达北京物流中心'),
            array('0'=>'1512778000','1'=>'已经为您派送')
        );
        return  $data;
    }
    /**
     *@abstract 生成订单订单编号
     * @return string
     */
    function ordrenumber(){
        return date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8).rand(1000,9999);
    }
}