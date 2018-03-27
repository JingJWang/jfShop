<?php
defined('BASEPATH') OR exit('No direct script access allowed');
header("Content-type:text/html;charset=utf-8;");
class init extends RR_Controller{
    /**
     * @abstract  首页
     * 取商品数据
     */
    function lists(){
        $this->str='h/init/lists';
        $result=$this->getList();
        $this->assign('sign', 0);
        $this->assign('result',$result);
        $this->display('index.html');
    }
    /**
     * 列表获取方式为ajax
     */
    function  ajaxlists(){
        $result=$this->getList();
        Often::Output($this->config->item('trues'),'','',$result);
    }
    /**
     *效验首页搜索参数
     *$data['price']  价格筛选  price=top 从低到高 price=bottom 从高到低 price='' 综合排序
     *$data['source']  来源筛选 
     *$data['classbig']  分类左边大类筛选
     *$data['classsmall']  分类右边小类筛选
     *$data['pri']价格区间筛选
     *$data['brand']品牌筛选
     *$data['name']产品名字筛选
     */
    function checkIndexData(){
        //声明一个空的数组 用来储存效验的数据
        $newData=array(
            'pri'=>'',
            'page'=>'',
            'name'=>'',
            'price'=>'',
            'brand'=>'',
            'source'=>'',
            'classbig'=>'',
            'classsmall'=>'',
        );
        $data=$this->input->post();
        if(isset($data['price']) && $data['price']!=''){
            $newData['price']=$data['price'];
        }
        if(isset($data['pri']) && $data['pri']!=''){
            $newData['pri']=$data['pri'];
        }
        if(isset($data['name']) && $data['name']!=''){
            $newData['name']=$this->unescape($data['name']);
        }
        if(isset($data['brand']) && $data['brand']!=''){
            $newData['brand']=str_replace(';',',',trim($data['brand'],';'));
        }
        if(isset($data['source']) && $data['source']!=''){
            $newData['source']=str_replace(';',',',trim($data['source'],';'));
        }
        if(isset($data['classbig']) && $data['classbig']!='' && $data['classbig']>0){
            $newData['classbig']=$data['classbig'];
        }
        if(isset($data['classsmall']) && $data['classsmall']!='' && $data['classsmall']>0){
            $newData['classsmall']=$data['classsmall'];
        }
        if(isset($data['page']) && $data['page']!='' && $data['page']>0){
            $newData['page']=$data['page'];
        }else{
            $newData['page']='';
        }
        return $newData;
    }
    /**
     * 回收模块 获取首页列表产品数据
     * @return Array
     */
    function getList(){
       $countData=array();
       $Countpage=0;//总页数
       $this->load->model('h/Goods_model');
       //获取首页搜索ajax提交的参数内容
       $newData=$this->checkIndexData();
       $this->Goods_model->data=$newData;
       $result=$this->Goods_model->goodList();
       //处理数组中的json 字符串
       if($result){
           foreach($result['result'] as $key=>$val){
               $result['result'][$key]['goods_source_info']=json_decode($val['goods_source_info']);
           } 
           if(!isset($result['res_result']['nums']) || $result['res_result']['nums']<10){
              $result['res_result']['nums']=1;
              $Countpage=$result['res_result']['nums'];//取总页数
              $newData['page']=1;//结果集小于10 page=1
           }else if($result['res_result']['nums']>=10){
               $Countpage=ceil($result['res_result']['nums']/10);//取总页数
               $newData['page']=2;//结果集大于10 page=1
           }
       }else{
           $result['result'] = '';
           $result['res_result'] = 0;
           $newData['page']=0; //没有查询到结果 page=0
       }
      $resue=array(
            'Countpage'=>$Countpage,
            'result'=>$result['result'],
            'name'=>$newData['name'],
            'page'=>$newData['page']
        );
        return $resue;
    }
    /**
     * 回收模块搜索功能 通过点击事件 获取品牌类别
    */
    function getTypeName(){
        $this->load->model('h/Goods_model');
        $result=$this->Goods_model->getTypeName();
        if($result){
            Often::Output($this->config->item('trues'),'','',$result);
        }else{
            Often::Output($this->config->item('false'));
        }
    }
    /**
     * 回收模块搜索功能 通过点击事件 获取品牌类别
     */
    function getTypeSmallName(){
        $id=$this->input->post('id',true);
        if(!isset($id) || !is_numeric($id) || $id<0){
            Often::Output($this->config->item('false'),$this->config->item('term_msg'));
        }
        $this->load->model('h/Goods_model');
        $this->Goods_model->id=$id;
        $result=$this->Goods_model->getTypeSamllName();
        if($result){
            Often::Output($this->config->item('trues'),'','',$result);
        }else{
            Often::Output($this->config->item('false'));
        }
    }
    /**
     * 回收模块 搜索功能通过点击分类  获取小分类名下的品牌
     */
    function getGoodClass(){
        $id=$this->input->post('id',true);
        if(!isset($id) || !is_numeric($id) || $id<0){
            Often::Output($this->config->item('false'),$this->config->item('term_msg'));
        }
        $this->load->model('h/Goods_model');
        $this->Goods_model->id=$id;
        $result=$this->Goods_model->getGoodClass();
        if($result){
            Often::Output($this->config->item('trues'),'','',$result);
        }else{
            Often::Output($this->config->item('false'));
        }
    }
    /**
     * @abstract  通过商品详情
     */
    function info($id){
        if(!isset($id) || !is_numeric($id) || $id<0){
            Often::Output($this->config->item('false'),$this->config->item('term_msg'));
        }
        //根据商品ID 查询内容
        $this->load->model('h/Goods_model');
        $this->Goods_model->id=$id;
        $result=$this->Goods_model->goodInfo();
        return $result;
    }
    /**
     * @abstract 加载搜索页面
     */
    function  findsearch(){
        $userid='';
        if($this->userid!=''){
            $userid=$this->userid;
        }else if($this->userid=='' && !empty($_SESSION['user']) && 
          (isset($_SESSION['user']['user_id']) && $_SESSION['user']['user_id']>0)){
            $userid=$_SESSION['user']['user_id'];
        }
        $this->load->model('h/Goods_model');
        $this->Goods_model->userid=$userid;
        $result=$this->Goods_model->selectSearchList();
        if($userid==''){
            $result['jintime']='';
        }
        $this->assign('result', $result);
        $this->display('r/search.html');
    }
    /**
     * @abstract 储存用户搜索的产品名字
     */
    function cunSearch(){
        $userid='';
        if($this->userid!=''){
            $userid=$this->userid;
        }else if($this->userid=='' && !empty($_SESSION['user']) &&
            (isset($_SESSION['user']['user_id']) && $_SESSION['user']['user_id']>0)){
            $userid=$_SESSION['user']['user_id'];
        }
        if(isset($userid)){
           $search=$this->input->post('search',true);
            if($search){
                $this->load->model('h/Goods_model');
                $this->Goods_model->name=$search;
                $this->Goods_model->userid=$userid;
                $result=$this->Goods_model->findSearch();
                if($result){
                    Often::Output($this->config->item('trues'));
                }else{
                    Often::Output($this->config->item('false'));
                }
            }
        }
    }
    /**
     * @abstract 删除最近搜索
     */
    function delSearch(){
        $userid='';
        if($this->userid!=''){
            $userid=$this->userid;
        }else if($this->userid=='' && !empty($_SESSION['user']) &&
            (isset($_SESSION['user']['user_id']) && $_SESSION['user']['user_id']>0)){
            $userid=$_SESSION['user']['user_id'];
        }
        $this->load->model('h/Goods_model');
        $this->Goods_model->userid=$userid;
        $result=$this->Goods_model->delSearch();
        if($result){
            Often::Output($this->config->item('trues'));
        }else{
            Often::Output($this->config->item('false'));
        }
    }
    /**
     * @abstract  商品详情
     */
    function goodsinfo(){
        $id=$this->input->get('id',true);
        if(!isset($id) || !is_numeric($id) || $id<0){
            Often::Output($this->config->item('false'),$this->config->item('term_msg'));
        }
        //根据商品ID 查询内容
        $this->load->model('h/Goods_model');
        $this->Goods_model->id=$id;
        $result=$this->Goods_model->goodInfo();
        if($result){
          $img=explode('||',$result['picture']);
          $yuanPrice=json_decode($result['sourceinfo']);
          $this->assign('yuanPrice', $yuanPrice->price);
          $this->assign('credit', $yuanPrice->credit);
          $this->assign('result', $result);  
          if(count($img)>1){
              array_pop($img);
          }
          $this->assign('img', $img);
        }else{
            Often::Output($this->config->item('false'),'查无此商品');
        }
        if(!empty($_SESSION['user'])){
            $userid=$_SESSION['user']['user_id'];
        }else{
            $userid='';
        }
        $this->assign('userid', $userid);
        $this->display('r/details.html');
    }
    /**
     * 效验从商品详情传的参数
     * sign 选择是金点1还是现金2
     * inpNum 数量
     */
    function checkDetailRecytl(){
        $data=$this->input->post();
        $newData=array();
        if(!isset($data['id']) || empty($data['id']) || !is_numeric($data['id']) || $data['id']<0){
            Often::Output($this->config->item('false'),$this->config->item('term_msg'));
        }
        $newData['id']=$data['id'];
        
        if(!isset($data['inpNum']) || empty($data['inpNum']) || !is_numeric($data['inpNum']) || $data['inpNum']<0){
            Often::Output($this->config->item('false'),$this->config->item('term_msg'));
        }
        $newData['inpNum']=$data['inpNum'];
        
        if(!isset($data['sign']) || empty($data['sign']) || !is_numeric($data['sign']) || $data['sign']<0){
            Often::Output($this->config->item('false'),$this->config->item('term_msg'));
        }
        $newData['sign']=$data['sign'];
        return $newData;
    }
    /**
     * 效验参数
     * sign 选择是金点1还是现金2
     * inpNum 数量
     */
    function checkNowRecytl(){
        $data=$this->input->post();
        $newData=array();
        $newOrderDa=array();
        $orderData=array();//把id method num 合并储存到该数组
    	$newData=explode(';',$data['check']);
        foreach($newData as $keydata=>$valdata){
        	$newOrderDa=explode('/',(trim($valdata,'[]')));
        	foreach($newOrderDa as $k=>$v){
        		if($data['state']==2 || ($data['state'] && $k>4)){
					if(!isset($newOrderDa[$k]) || empty($newOrderDa[$k]) || !is_numeric($newOrderDa[$k]) || $newOrderDa[$k]<0){
	           			Often::Output($this->config->item('false'),$this->config->item('term_msg'));
		        	}        			
        		}
				$orderData[$keydata]['id']=$newOrderDa[0];
				$orderData[$keydata]['method']=$newOrderDa[1];
				$orderData[$keydata]['num']=$newOrderDa[2];
				$orderData[$keydata]['packid']=$newOrderDa[3];
        	}
        }
         return $orderData;
    }
	 /**
     * 效验从商品详情传的参数 
     * sign 选择是金点1还是现金2
     * inpNum 数量
     */
    function checkGetRecytl(){
        $data=$this->state;
		$newdata=explode('/',$data);
		$zonghe=array();
		if($newdata['0'] && $newdata['0']>0){
			$zonghe['state']=$newdata['0'];
		}
		if($newdata[1]){
		    //拆分数组 然后去掉空的元素
			$firstArray=array_filter(explode(';',$newdata[1]));
			foreach($firstArray as $key=>$val){
				$array=explode('-',$val);
				if(is_array($array)){
					if(!isset($array[0]) || empty($array[0]) || !is_numeric($array[0]) || $array[0]<0){
						Often::Output($this->config->item('false'),$this->config->item('term_msg'));
					}
					$zonghe[$key]['id']=$array[0];
					
					if(!isset($array[1]) || empty($array[1]) || !is_numeric($array[1]) || $array[1]<0){
						Often::Output($this->config->item('false'),$this->config->item('term_msg'));
					}
					$zonghe[$key]['inpNum']=$array[1];
					
					if(!isset($array[2]) || empty($array[2]) || !is_numeric($array[2]) || $array[2]<0){
						Often::Output($this->config->item('false'),$this->config->item('term_msg'));
					}
					$zonghe[$key]['sign']=$array[2];
				}
			}
		}
        return $zonghe;
    }
     /**
      * @abstract  确认页面
      */
     function confirmOrder(){
         $method = !empty($_GET) ? 'GET' : 'POST';
         if(!empty($_POST)){
            $this->state=$this->input->post('state');
         }else{
            $this->state=$this->input->get('state');
         }
		 $zonghe=$this->checkGetRecytl();
         if($zonghe && isset($zonghe['state']) && $zonghe['state']==1){
             //直接购买
             //查询商品内容
             $id=$zonghe[0]['id'];
             $goods=$this->info($id);
             $getSess=$zonghe[0];
             if(!$goods || !$getSess){
                 Often::Output($this->config->item('false'));
             }
             $goods['sourceinfo']=json_decode($goods['sourceinfo']);
             $this->assign('goods', $goods);
             $this->assign('getSess', $getSess);
             $this->assign('state', $zonghe['state']);
         }else if($zonghe && isset($zonghe['state']) && $zonghe['state']==2){
             $ids=array_column($zonghe,'id');
             //获取从回收车提取的id=回收id
             $packid=implode(',',$ids);
             $this->load->model('h/Goods_model');
             $this->Goods_model->id=$packid;
             $this->Goods_model->userid=$this->userid;
             $result=$this->Goods_model->goodDuoInfo();
             foreach($result as $key=>$val){
                 $result[$key]['sourceinfo']=json_decode($val['sourceinfo']);
             }
             $this->assign('getSess', $result);
             $this->assign('state', $zonghe['state']);
         }
         $this->display('r/order.html');
     }
     /**
      * @abstract 效验提交订单的名字跟联系电话
      */
     function checkSubOrder(){
         $data=$this->input->post();
         //校验手机类型ID
         if(empty($data['phone']) || !is_numeric($data['phone']) || $data['phone'] < 0){
             Often::Output($this->config->item('false'),$this->config->item('term_msg'));
         }
         $newData['phone'] = $data['phone'];
         //校验手机名称
         if(empty($data['name']) && preg_match('/^[\x{4e00}-\x{9fa5}]+$/u', $data['name']) < 0){
             Often::Output($this->config->item('false'),$this->config->item('term_msg'));
         }
         $newData['name'] = $data['name'];
         return $newData;
     }
     /**
      * @abstract提交订单
      */
     function  submitOrder(){
         //校验参数
         $newData=$this->checkNowRecytl();
         $newSubOrder=$this->checkSubOrder();
         $countData=array();
         if($newData && $newSubOrder){
             $countData['name']=$newSubOrder['name'];
             $countData['phone']=$newSubOrder['phone'];
             //根据传进来验证过的参数 保存到订单表里面
             $this->load->model('h/Goods_model');
             $this->Goods_model->new_data=$newData;
             $this->Goods_model->data_order=$countData;
             $this->Goods_model->userid=$this->userid;
             $orderId=$this->Goods_model->insertOrder();
             if($orderId){
                 Often::Output($this->config->item('trues'),'','subsucc?orderid='.$orderId,$orderId);
             }
         }else {
             Often::Output($this->config->item('false'),$this->config->item('term_msg'));
         }
         //提交成功跳转提示页面
     }
     /**
      * @abstract 订单提交成功跳转页面
      */
     function subsucc(){
         $orderid=$this->input->get('orderid',true);
         if(!isset($orderid) || !is_numeric($orderid) || empty($orderid)){
             Often::Output($this->config->item('false'),$this->config->item('term_msg'));
         }
         $urls='orderLists';
         $this->assign('url', $urls);
         $this->display('r/subsucc.html');
     }
     /**
      * 通过订单编号查询订单详情
      */
     function orderDetail(){
         $userid=$this->userid;
         $orderid=$this->input->get('orderId',true);
         if(!isset($orderid) || !is_numeric($orderid) || empty($orderid)){
             Often::Output($this->config->item('false'),$this->config->item('term_msg'));
         }
         $this->load->model('h/Goods_model');
         $this->Goods_model->orderNum=$orderid;
         $this->Goods_model->userid=$userid;
         $result=$this->Goods_model->orderDetail();
         if($result){
             $this->assign('resuOrder', $result['order']);
             $this->assign('results', $result['result']);
         }else{
             $this->assign('resuOrder', '');
             $this->assign('results', '');
         }
         $this->display('r/orderDetail.html');
     }
     /**
      * 效验快递参数
      */
     function checkKdOrder(){
         $data=$this->input->post();
         //校验快递单号
         if(empty($data['kdNum']) || !is_numeric($data['kdNum']) || !isset($data['kdNum'])){
             Often::Output($this->config->item('false'),$this->config->item('term_msg'));
         }
         $newData['kdNum'] = $data['kdNum'];
         //校验快递名字
         if(empty($data['kdname']) && preg_match('/^[\x{4e00}-\x{9fa5}]+$/u', $data['kdname']) < 0){
             Often::Output($this->config->item('false'),$this->config->item('term_msg'));
         }
         $newData['kdname'] = $data['kdname'];
         //校验快递名字
         if(empty($data['orderNum']) || !isset($data['orderNum'])){
             Often::Output($this->config->item('false'),$this->config->item('term_msg'));
         }
         $newData['orderNum'] = $data['orderNum'];
         return $newData;
     }
     /**
      * @abstract 修改订单的物流信息
      */
     function upOrderTransport(){
         $data=$this->checkKdOrder();
         if($data){
             $this->load->model('h/Goods_model');
             $this->Goods_model->data=$data;
             $this->Goods_model->userid=$this->userid;
             $result=$this->Goods_model->upOrderTransport();
             if($result){
                 Often::Output($this->config->item('trues'));
             }else{
                 Often::Output($this->config->item('false'));
             }
         }else{
             Often::Output($this->config->item('false'));
         }
     }
     /**
      *加入商品进入购物车
      */
     function insertCart(){
         //校验参数
         $newData=$this->checkDetailRecytl();
         if($newData){
             //根据传进来验证过的参数 保存到回收车表里面
             $this->load->model('h/Goods_model');
             $this->Goods_model->id=$newData['id'];
             $this->Goods_model->num=$newData['inpNum'];
             $this->Goods_model->method=$newData['sign'];
             $this->Goods_model->userid=$this->userid;
             $result=$this->Goods_model->insertPackage();
             if($result){
                 Often::Output($this->config->item('trues'),'','',$result);
             }else{
                 Often::Output($this->config->item('false'));
             }
         }else{
             Often::Output($this->config->item('false'));
         }
         
     }
     /**
      * 查看购物车
      */
     function listCart(){
         //登录查看的购物车
         //效验登录的userid 与保存到购物车的userid 是否存在并保持一致
         if($this->userid && !empty($this->userid) && is_numeric($this->userid) && $this->userid>0){
              $this->load->model('h/Goods_model');
              $this->Goods_model->userid=$this->userid;
              $result=$this->Goods_model->listCart();
              if($result){
                  $this->assign('result', $result);
              }else{
                  $this->assign('result', '');
              }
          }
          $this->display('r/shopCart.html');
     }
     /**
      * 效验删除回收车物品的参数
      */
     function checkDelCart(){
         $id=$this->input->post('id',true);
         $newId='';
         if(!isset($id) || empty($id)){
             Often::Output($this->config->item('false'),$this->config->item('term_msg'));
         }
         
         $newId=str_replace(';',',',substr($id, 0, -1));
         return $newId;
     }
     /**
      * 删除回收车的东西
      */
     function delCart(){
         $newId=$this->checkDelCart();
         if($newId){
             $this->load->model('h/Goods_model');
             $this->Goods_model->id=$newId;
             $this->Goods_model->userid=$this->userid;
             $result=$this->Goods_model->delCart();
             if($result){
                 Often::Output($this->config->item('trues'),'','listCart',$this->userid);
             }else{
                  Often::Output($this->config->item('false'));
             }
         }else{
              Often::Output($this->config->item('false'));
         }
     } 
     /**
      * 效验回收车修改数据的参数
      */
     function checkUpCart(){
         $newData=array(
             'packid'=>'',
             'num'=>''
         );
        $data=$this->input->post();
        if(isset($data['packid']) && is_numeric($data['packid']) && $data['packid']>0){
            $newData['packid']=$data['packid'];
        }
        if(isset($data['num']) && is_numeric($data['num']) && $data['num']>0){
            $newData['num']=$data['num'];
        }
        return $newData;
     }
     /**
      * 修改回收车表的单个产品的记录
      */
     function updateCart(){
         //效验参数
         $resul=$this->checkUpCart();
         $this->load->model('h/Goods_model');
         $this->Goods_model->resul=$resul;
         $this->Goods_model->userid=$this->userid;
         $result=$this->Goods_model->updateCart();
         if($result){
            Often::Output($this->config->item('trues'));
         }
         Often::Output($this->config->item('false'));
     }
     /**
      * 回收订单列表
      */
     function orderLists(){
         $this->display('r/list.html');
     }
     /**
      * 获取订单列表数据
      */
     function getOrderList(){
         $state='';//存储订单状态
         $status=$this->input->post('status',true);
         if(empty($status) || !isset($status)){
             Often::Output($this->config->item('false'),$this->config->item('term_msg'));
         }
         $page=$this->input->post('page',true);
         if(!isset($page)){
             Often::Output($this->config->item('false'),$this->config->item('term_msg'));
         }
         switch($status){
             case 'wait':
                 //待发货
                 $state=2;break;
             case 'alreadly':
                 //已发货
                 $state=3;break;
             case 'complated':
                 //已完成
                 $state=10;break;
             case 'cancles':
                 //已取消
                 $state=4;break;
            case 'all':
                 //全部
                 $state=5;break;
            default:;break;
         }
         $this->load->model('h/Goods_model');
         $this->Goods_model->state=$state;
         $this->Goods_model->page=$page;
         $this->Goods_model->userid=$this->userid;
         $result=$this->Goods_model->orderLists();
         if(!$result){
             $result='';
         }
         if($result['nums']['nums']>0){
             $result['nums']=ceil($result['nums']['nums']/10);
         }else{
             $result['nums']=0;
         }
         Often::Output($this->config->item('trues'),'','',$result);
     }
     
     /**
      * @abstract 取消订单
      */
     function cancelOrder(){
         $userid=$this->userid;
         $orderid=$this->input->post('orderId',true);
         if(empty($orderid)){
             Often::Output($this->config->item('false'),$this->config->item('term_msg'));
         }
         $this->load->model('h/Goods_model');
         $this->Goods_model->orderid=$orderid;
         $this->Goods_model->userid=$userid;
         $result=$this->Goods_model->cancelOrder();
         if($result){
             Often::Output($this->config->item('trues'));
         }else{
             Often::Output($this->config->item('false'));
         }
     } 
     /**
      *查看物流记录
      */
     function expressRecord(){
        // $number=$this->input->post('kdnumber',true);
        $number='12345578';
         if(empty($number)){
             Often::Output($this->config->item('false'),$this->config->item('term_msg'));
         }
         $this->load->model('h/Goods_model');
         $this->Goods_model->number=$number;
         $result=$this->Goods_model->expressRecord();
         if($result){
             Often::Output($this->config->item('trues'),'','',$result);
         }else{
             Often::Output($this->config->item('false'));
         }
         
     }
     /**
      * 解析由js传过来的escape编码
      */
     private function unescape($str){
         $ret = '';
         $len = strlen($str);
         for ($i = 0; $i < $len; $i++){
             if ($str[$i] == '%' && $str[$i+1] == 'u'){
                 $val = hexdec(substr($str, $i+2, 4));
                 if ($val < 0x7f) $ret .= chr($val);
                 else if($val < 0x800) $ret .= chr(0xc0|($val>>6)).chr(0x80|($val&0x3f));
                 else $ret .= chr(0xe0|($val>>12)).chr(0x80|(($val>>6)&0x3f)).chr(0x80|($val&0x3f));
                 $i += 5;
             }
             else if ($str[$i] == '%'){
                 $ret .= urldecode(substr($str, $i, 3));
                 $i += 2;
             }
             else $ret .= $str[$i];
         }
         return $ret;
     }
}