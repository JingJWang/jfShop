<?php
defined('BASEPATH') OR exit('No direct script access allowed');
header("Content-type:text/html;charset=utf-8;");
class init extends RR_Controller{
    /**
     * @abstract  首页
     * 取商品数据
     */
    function lists(){
        $result=$this->getList();
        $this->assign('result',$result);
        $this->display('s/shop.html');
    }
    /**
     * 列表获取方式为ajax
     */
    function  ajaxlists(){
        $result=$this->getList();
        Often::Output($this->config->item('trues'),'','',$result);
    }
    /**
     * 效验表单提交ajax 是get 还是post
     */
    function  checkMethodAjax(){
        $reqajax=$this->input->is_ajax_request();
        if($reqajax == 'ajax'){
            return 1;
        }else{
            return 0;
        }
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
            $newData['page']='0';
        }
        return $newData;
    }
    /**
     * @abstract  购买商品 商城首页列表
     */
    function  getList(){
        $countData=array();
        $Countpage=0;//总页数
        $this->load->model('s/Shop_model');
        //获取首页搜索ajax提交的参数内容
        $indexData=$this->checkIndexData();
        //$method=$this->checkMethodAjax();
        $this->Shop_model->data=$indexData;
        $result=$this->Shop_model->goodList();
        //处理数组中的json 字符串
        if($result){
         foreach($result['result'] as $key=>$val){
               $result['result'][$key]['shop_source_info']=json_decode($val['shop_source_info']);
           } 
           if(!isset($result['res_result']['nums']) || $result['res_result']['nums']<10){
              $result['res_result']['nums']=1;
              $Countpage=$result['res_result']['nums'];//取总页数
              //$indexData['page']=1;//结果集小于10 page=1
           }else if($result['res_result']['nums']>=10){
               $Countpage=ceil($result['res_result']['nums']/10);//取总页数
               ///$indexData['page']=2;//结果集大于10 page=1
           }
        }else{
           $result['result'] = '';
           $result['res_result'] = 0;
          // $indexData['page']=0; //没有查询到结果 page=0
        }
        $resue=array(
            'Countpage'=>$Countpage,
            'result'=>$result['result'],
            //'name'=>$indexData['name'],
            'page'=>$indexData['page'],
            'param'=>$indexData
        );
       return $resue;
    }
    /**
     * 商城模块搜索功能 通过点击事件 获取品牌类别
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
        $this->load->model('s/Shop_model');
        $this->Shop_model->id=$id;
        $result=$this->Shop_model->goodInfo();
        return $result;
    }
    /**
     * @abstract  通过商品id post 获取商品详情
     */
    function getInfo(){
        $id=$this->input->post('id',true);
        if(!isset($id) || !is_numeric($id) || $id<0){
            Often::Output($this->config->item('false'),$this->config->item('term_msg'));
        }
        //根据商品ID 查询内容
        $this->load->model('s/Shop_model');
        $this->Shop_model->id=$id;
        $result=$this->Shop_model->getshopInfo();
        if($result){
            Often::Output($this->config->item('trues'),'','',$result);
        }else{
            Often::Output($this->config->item('false'));
        }
        return $result;
    }
    /**
     * @abstract  商城搜索
     */
    function Shopsear(){
        $userid='';
        if($this->userid!=''){
            $userid=$this->userid;
        }else if($this->userid=='' && !empty($_SESSION['user']) &&
            (isset($_SESSION['user']['user_id']) && $_SESSION['user']['user_id']>0)){
            $userid=$_SESSION['user']['user_id'];
        }
        $this->load->model('s/Shop_model');
        $this->Shop_model->usersid=$userid;
        $result=$this->Shop_model->selectSearchList();
        if($userid==''){
            $result['jintime']='';
        }
        $this->assign('result', $result);
        $this->display('s/shopsear.html');
    }
    /**
     * @abstract 加载搜索页面
     */
    function findsearch(){
        $userid='';
        if($this->userid){
            $userid=$this->userid;
        }else if($this->userid=='' && !empty($_SESSION['user']) && 
          (isset($_SESSION['user']['user_id']) && $_SESSION['user']['user_id']>0)){
            $userid=$_SESSION['user']['user_id'];
        }
        $this->load->model('s/Shop_model');
        $this->Shop_model->userid=$userid;
        $result=$this->Shop_model->selectSearchList();
        $this->assign('userid', $userid);
        $this->assign('result', $result);
        $this->display('r/search.html');
    }
    /**
     * @abstract 储存用户搜索的产品名字
     */
    function cunSearch(){
        $userid=$this->userid;
        if($this->userid!=''){
            $userid=$this->userid;
        }else if($this->userid=='' && !empty($_SESSION['user']) &&
            (isset($_SESSION['user']['user_id']) && $_SESSION['user']['user_id']>0)){
            $userid=$_SESSION['user']['user_id'];
        }
        if(isset($userid)){
            $search=$this->input->post('search',true);
            if($search){
                $this->load->model('s/Shop_model');
                $this->Shop_model->name=$search;
                $this->Shop_model->userid=$userid;
                $result=$this->Shop_model->findSearch();
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
        if($this->userid!='' && !isset($_SESSION['user'])){
            $userid=$this->userid;
        }else if(isset($_SESSION['user']) && isset($_SESSION['user']['user_id']) && $_SESSION['user']['user_id']>0){
            $userid=$_SESSION['user']['user_id'];
        }
        $this->load->model('s/Shop_model');
        $this->Shop_model->userid=$userid;
        $result=$this->Shop_model->delSearch();
        if($result){
            Often::Output($this->config->item('trues'));
        }else{
            Often::Output($this->config->item('false'));
        }
    }
    /**
     * @abstract 购买商品  显示商品详情
     * @param    id int 商品id
     */
    function shopinfo(){
        $userid='';
        $this->load->model('s/Shop_model');
        if($this->userid && !empty($this->userid) && $this->userid>0){
            $userid=$this->userid;
            //根据用户ID查询购物车商品数量
            $this->load->model('s/Shop_model');
            $this->Shop_model->userid=$userid;
            $countCarNum=$this->Shop_model->selectCartNum();
            if($countCarNum){
                $count=$countCarNum['num'];
            }else{
                $count='';
            }
        }else{
            if(isset($_SESSION['user']['user_id'])){
                $userid=$_SESSION['user']['user_id'];
            }else{
                $userid='';
            }
            $count='';
        }
        //校验商品ID
        $id=$this->input->get('id',true);
        if(!isset($id) || !is_numeric($id) || $id<0){
            Often::Output($this->config->item('false'),$this->config->item('term_msg'),'','');
        }
        //根据商品ID 查询内容
        $this->Shop_model->busineid=$id;
        $result=$this->Shop_model->goodInfo();
        if($result){
            $yuanPrice=json_decode($result[0]['sourceinfo']);
            $img=explode('||',$result[0]['picture']);
            if(count($img)>1){
                array_pop($img);
            }
            $this->assign('img', $img);
            $this->assign('yuanPrice', $yuanPrice->price);
            $this->assign('credit', $yuanPrice->credit);
            $this->assign('result', $result[0]);
        }else{
            Often::Output($this->config->item('false'),'读取不到商品','','');
        }
        $this->assign('userid',$userid);
        $this->assign('count',$count);
        $this->display('s/particulars.html');
    }
    /**
     * @abstract  购买商品  加入购物车 校验参数是否符合规范
     * @param   shopid   int  商品ID
     * @param   busineid   int  商户的商品编号
     * @param   inpNum   int  数量
     * @return  校验通过返回参数数组 array  校验失败返回json string
     */
    function checkDetailRecytl(){
        $data=$this->input->post();
        $newData=array();
        if(!isset($data['shopid']) || empty($data['shopid']) || !is_numeric($data['shopid']) || $data['shopid'] < 0){
            Often::Output($this->config->item('false'),$this->config->item('term_msg'));
        }
        $newData['shopid']=$data['shopid'];
        if(!isset($data['busineid']) || empty($data['busineid']) || !is_numeric($data['busineid']) || $data['busineid'] < 0){
            Often::Output($this->config->item('false'),$this->config->item('term_msg'));
        }
        $newData['busineid']=$data['busineid'];
        if(!isset($data['inpNum']) || empty($data['inpNum']) || !is_numeric($data['inpNum']) || $data['inpNum'] < 0 ){
            Often::Output($this->config->item('false'),$this->config->item('term_msg'));
        }
        $newData['inpNum']=$data['inpNum'];
        return $newData;
    }
    /**
     * @abstract 购买商品  添加商品到购物车
     * @param   shopid   int  商品ID
     * @param   inpNum   int  数量
     * @return  json 
     */
    function addshopcart(){
    	//检查参数
    	$newData=$this->checkDetailRecytl();
	    $this->load->model('s/Shop_model');
	    $this->Shop_model->shopid=$newData['shopid'];
	    $this->Shop_model->busineid=$newData['busineid'];
	    $this->Shop_model->num=$newData['inpNum'];
	    $this->Shop_model->userid=$this->userid;
	    $countCarNum=$this->Shop_model->insertCart();
	    if($countCarNum){
	       Often::Output($this->config->item('trues'));
	    }else{
	       Often::Output($this->config->item('false'),'商品加入到购物车出现异常');
	    }
    }
    /**
     * @abstract  购买商品  查看购物车详情
     * @param  userid int 用户id
     */
    function  shopcart(){
        //根据用户id 读取购车详情
        $this->load->model('s/Shop_model');
        $this->Shop_model->userid=$this->userid;
        $result=$this->Shop_model->selectCart();
        if(!$result){
        	$result='';
        }
		$this->assign('result',$result);
        //通过userid 查询用户购物车的商品数量
        $this->display('s/cart.html');
    }
    /**
     * @abstract 购买商品  购物车确认订单
     */
    function confirmMany(){
        //校验商品ID
        $id=$this->input->get('id',true);
        $checkid=str_replace(',', '', $id);
        if(empty($checkid) || !is_numeric($checkid) || $checkid < 0 ){
            Often::Output($this->config->item('false'),'非法请求!');
        }
        //读取用户的默认地址
        $addres=$this->defaultAddress();
        //根据商品ID读取商品详情
        $this->load->model('s/Shop_model');
        $this->Shop_model->id=trim($id,',');
        $result=$this->Shop_model->shopDuoInfo();
        if($result === false){
            Often::Output($this->config->item('false'),'获取商品详情出现异常!');
        }
        foreach ($result as $key=>$val){
            $result[$key]['sourceinfo']=json_decode($val['sourceinfo']);
        }
        $this->assign('getSess', $result);
        $this->assign('addres', $addres);
        $this->assign('state', 'order');//?
        $this->display('s/sdetail.html');
    }
    /**
     * @abstract  购买商品   普通流程购买 直接立即购买
     * @param  userid  int 用户id single
     */
    function confirmSingle(){
        //验证表单提交的数据
        $id=$this->input->get('id',true);
        $count=explode(';',$id);
        if(empty($count['0']) || !is_numeric($count['0']) || $count['0'] < 0 ){
            Often::Output($this->config->item('false'),'非法请求!');
        }
        $num=explode('=', $count['1']);
        if(empty($num['1']) || !is_numeric($num['1']) || $num['1'] < 0 ){
            Often::Output($this->config->item('false'),'非法请求!');
        }
        //读取用户的默认地址
        $addres=$this->defaultAddress();
        //根据商品ID读取商品详情 
        $this->load->model('s/Shop_model');
        $this->Shop_model->busineid=$count['0'];
        $result=$this->Shop_model->goodInfo();
        if($result === false){
            Often::Output($this->config->item('false'),'获取商品详情出现异常!');
        }
        foreach ($result as $key=>$val){
            $result[$key]['sourceinfo']=json_decode($result[$key]['sourceinfo']);
        }
        $result['0']['num']=$num['1'];
        $result['0']['packageid']=0;
        $this->assign('getSess', $result);
        $this->assign('addres', $addres);
        $this->assign('state', 'order');//?
        $this->display('s/sdetail.html');
    }
    /**
     * @abstract 购买商品  提交订单  校验参数 根据传递的购物车订单ID
     *           读取购买商品所需要的数据
     * @param  method int 支付方式   1金点支付  2 微信支付
     * @param  shopid int 商品id
     * @param  addrId int 用户收货地址ID 
     * @return  传递参数符合过过饭返回array  否则返回json
     * 
     */
    function checkNowRecytl(){
        //校验传递的收货地址ID
        $addrId=$this->input->post('addressId',true);
        if(empty($addrId) || $addrId < 0 || !is_numeric($addrId)){
            Often::Output($this->config->item('false'),'提交订单出现异常!');
        }
        //校验传递的商品ID
        $shopid=$this->input->post('shopid',true);
        $check_shopid=str_replace(',', '', implode(',',$shopid));
        if(!is_array($shopid) || $check_shopid < 0 || !is_numeric($check_shopid)){
            Often::Output($this->config->item('false'),'提交订单出现异常!');
        }
        //校验传递的商户商品ID
        $busineid=$this->input->post('busineid',true);
        $check_busineid=str_replace(',', '', implode(',',$busineid));
        if(!is_array($busineid) || $check_busineid < 0 || !is_numeric($check_busineid)){
            Often::Output($this->config->item('false'),'提交订单出现异常!');
        }
        //校验传递的购物车物品ID
        $packid=$this->input->post('packid',true);
        $check_packid=str_replace(',', '', implode(',', $packid));
        if(!is_array($packid) || !is_numeric($check_packid) || $check_packid < 0){
            Often::Output($this->config->item('false'),'提交订单出现异常!');
        }
        //校验传递的购买物品数量
        $num=$this->input->post('num',true);
        $check_num=str_replace(',', '', implode(',', $packid));
        if(!is_array($num) || !is_numeric($check_num) || $check_num < 0){
            Often::Output($this->config->item('false'),'提交订单出现异常!');
        }
        //校验传递的交易方式
        $pay=$this->input->post('hidPay',true);
        if(empty($pay) || $pay < 0 || !is_numeric($pay)){
            Often::Output($this->config->item('false'),'提交订单出现异常!');
        }
        //判断是单订单提交 还是购物车提交
        if(empty($check_packid)){
            $orderData['shopid']=trim(implode(',',$shopid),',');
            $orderData['busineid']=trim(implode(',',$busineid),',');
            $orderData['packid']=0;;
            $orderData['num']=trim(implode(',', $num),',');
            $orderData['many']=0;//单订单提交
        }else{
            $orderData['shopid']=0;
            $orderData['packid']=trim(implode(',',$packid),',');
            $orderData['busineid']=trim(implode(',',$busineid),',');
            $orderData['num']=0;
            $orderData['many']=1;//多订单提交
        }
        $orderData['pay']=$pay;
        $orderData['addrId']=$addrId;
        return $orderData;
    }
    
    /**
     * @abstract  购买商品 单商品购买
     * @return  array 商品读取成功返回商品集合 否则返回bool  false
     */
    function selGoodsInfo($option){
        $this->load->model('s/Shop_model');
        $this->Shop_model->shopid=$option['shopid'];
        $this->Shop_model->busineid=$option['busineid'];
        $data=$this->Shop_model->goodInfo();
        if($data === false){
            Often::Output($this->config->item('false'),'提交订单出现异常!');
        }
        $pri=0;//价格
        $inte=0;//积分
        if($option['pay'] == 2){//1是金点 2是金钱//3综合 金点和钱
            $pri  +=  $option['num']*$data[0]['price'];
        }else if($option['pay'] == 1){
            $inte +=  $option['num']*$data[0]['integral'];
        }
        // 商品id - 购买个数
        $shopid  = '['.$data[0]['shopid'].'-'.$option['num'].']';
        //组合商品订单内商品内容
        $info=array(
                'busineid'=>$data[0]['busineid'].';',//商户商品的编号
                'pri'=>$pri,//商品RMB价格
                'inte'=>$inte,//商品积分价格
                'shpid'=>$shopid,//商品ID-商品数量
                'type'=>1,//订单类行
                'pay'=>$option['pay'],//订单交易方式
                'row'=>$option['num'],//包含商品的数量
                'many'=>$option['many']//是否是购物车提交订单 是1 否0
        );
        return $info;
    }
    /**
     * @abstract  购买商品  根据购物车商品ID查询商品详细内容
     * @param   array   $option  购车内物品ID 购买方式  地址ID
     * @return  array 商品读取成功返回商品集合 否则返回bool  false
     */
    function selGoodsCarInfo($option){
        $this->load->model('s/Shop_model');
        $this->Shop_model->userid=$this->userid;
        $this->Shop_model->packid=$option['packid'];
        $this->Shop_model->busineid=$option['busineid'];
        $data=$this->Shop_model->selGoods();
        if($data === false){
            Often::Output($this->config->item('false'),'提交订单出现异常!');
        }
        $pri=0;//价格
        $inte=0;//积分
        $shopid='';//商品ID-购买数量
        $busineid='';//商户商品的编号
        foreach ($data as $key=>$val){
            if($option['pay'] == 2){//1金点 2金钱
                $pri  +=  $val['ordernum']*$val['price'];
            }else if($option['pay'] == 1){
                $inte +=  $val['ordernum']*$val['integral'];
            }else if($option['pay'] == 3){
                if($val['integral']>0){
                    $inte +=  $val['ordernum']*$val['integral'];
                }else{
                    $pri +=  $val['ordernum']*$val['price'];
                }
                //$inte +=  $val['ordernum']*$val['integral']+$val['ordernum']*$val['price'];
            }
            $busineid .=$val['busineid'].';';
            $shopid  .= '['.$val['shopid'].'-'.$val['ordernum'].'],';
        }
        //组合商品订单内商品内容
        $info=array(
                'busineid'=>$busineid,
                'pri'=>$pri,//商品RMB价格
                'inte'=>$inte,//商品积分价格
                'shpid'=>trim($shopid,','),//商品ID-商品数量
                'type'=>2,//订单类行
                'pay'=>$option['pay'],//订单交易方式
                'row'=>sizeof($data),//包含商品的数量
                'many'=>$option['many']//是否是购物车提交订单 是1 否0
        );
        return $info;
    }
    
    /**
     * @abstract 购买商品  提交订单
     * @param  addrId int  地址ID
     * @param  packid string  购物车商品ID 28,29,
     * @param  method int 支付方式
     */
    function sureOrder(){
        //校验提交的订单是否符合规范
        $newData=$this->checkNowRecytl();
        //根据购物车商品ID查询商品详细内容
        if($newData['many'] == 1){//购物车提交
            $goods=$this->selGoodsCarInfo($newData);
        }else{
            $goods=$this->selGoodsInfo($newData);
        }
        //获取购买的商品在商户商品表的编号
         //根据传进来验证过的参数 保存到订单表里面
        $this->load->model('s/Shop_model');
        $this->Shop_model->addrId=$newData['addrId'];
        $this->Shop_model->shopinfo=$goods;
        $this->Shop_model->userid=$this->userid;
        $this->Shop_model->packageid=$newData['packid'];
        $this->Shop_model->many=$newData['many'];
        $orderId=$this->Shop_model->insertOrder();
        if($orderId){
            $url='intoJdpay?orderId='.$this->Shop_model->orderNum;
            header("Location:$url");
        }else {
            Often::Output($this->config->item('false'),$this->config->item('term_msg'),'','');
        }
    }
    /**
     * @abstract 地址管理    读取默认地址
     * @param userid  int 用户id
     */
    function defaultAddress(){
        //根据用户id  读取用户的默认地址信息
        $this->load->model('s/Shop_model');
        $this->Shop_model->userid=$this->userid;
        $this->Shop_model->state=2;
        $result=$this->Shop_model->selectDefAdd();
        if($result){
            return $result;
        }else{
            return '';
        }
    }
    /**
     * 效验购物车商品的参数
     */
    function checkDelCart(){
        $id=$this->input->post('id',true);
        $newData=array();
        if(!isset($id) || empty($id)){
            Often::Output($this->config->item('false'),$this->config->item('term_msg'),'','');
        }
        //$newData=explode(';',$id);
        $newId=str_replace(';',',',substr($id, 0, -1));
        return $newId;
    }
    /**
     * 存储用户从购物车购买商品
     */
    function nowCartRecytl(){
        $newId=$this->checkDelCart();
        if($newId){
            //查询相关物品的相关信息
            $this->load->model('s/Shop_model');
            $this->Shop_model->id=$newId;
            $result=$this->Shop_model->shopDuoInfo();
            $_SESSION['shop']=$result;
            $userid=$this->userid;
            if($result){
                Often::Output($this->config->item('trues'),'','',$userid);
            }else{
                Often::Output($this->config->item('false'));
            }
        }else{
            Often::Output($this->config->item('false'));
        }
    }
    /**
     * 购物车删除商品
     */
    function  delcart(){
        $newId=$this->checkDelCart();
        if($newId){
            $this->load->model('s/Shop_model');
            $this->Shop_model->id=$newId;
            $userid=$this->userid;
            $result=$this->Shop_model->delCart();
            if($result){
                Often::Output($this->config->item('trues'),'','shopcart',$userid);
            }else{
                Often::Output($this->config->item('false'));
            }
        }else{
            Often::Output($this->config->item('false'));
        }
    }
    /**
     * 效验购物车修改数据的参数
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
     * 修改购物车表的单个产品的记录
     */
    function updateCart(){
        $userid=$this->userid;
        //效验参数
        $resul=$this->checkUpCart();
        $this->load->model('s/Shop_model');
        $this->Shop_model->resul=$resul;
        $this->Shop_model->userid=$userid;
        $result=$this->Shop_model->updateCart();
        if($result){
            Often::Output($this->config->item('trues'));
        }
        Often::Output($this->config->item('false'));
    }
     /**
      * @abstract 订单支付   进入到金点密码支付页面
      */
     function intoJdpay(){
         //校验订单ID
         $orderId=$this->input->get('orderId',true);
         if(empty($orderId) ||!is_numeric($orderId) || $orderId < 0){
             Often::Output($this->config->item('false'),'支付出现异常!');
         }
         //根据传进来验证过的参数 保存到订单表里面
         $this->load->model('s/Shop_model');
         $this->Shop_model->orderNum=$orderId;
         $this->Shop_model->userid=$this->userid;
         $result=$this->Shop_model->orderDetail();
         if($result){
             $money=$result['order_integral']/100;
         }else{
             $money='';
         }
         $this->assign('money',$money);
     	 $this->display('s/pay.html');
     }
     /**
      * @abstract  订单支付  金点支付 点击提交 验证支付密码
      */
     function checkPayPwd(){
        $data=$this->input->post();
        $this->load->model('s/Shop_model');
        $this->Shop_model->orderId=$data['orderId'];
        $this->Shop_model->userid=$this->userid;
        $orderId=$this->Shop_model->selectOrder();
        if($orderId){
            Often::Output($this->config->item('trues'),'','succOrder?orderId='.$orderId);
        }else {
            Often::Output($this->config->item('false'),$this->config->item('term_msg'),'','');
        }
     }
     /**
      * 进入到订单支付成功页面
      */
     function succOrder(){
         $orderId=$this->input->get('orderId',true);
         if($orderId=='' || empty($orderId)){
             Often::Output($this->config->item('false'),$this->config->item('term_msg'),'','');
         }
         $this->assign('orderId',$orderId);
         $this->display('s/paysucc.html');
     }
    
    
    
    /********************订单模块*************/
     /**
     * @abstract 我的订单列表 买入的
     * state 状态
     */
     function myshopList(){
         $this->display('busine/mylist.html');
     }
     /**
      * @abstract 我的订单已买
      */
     function buyOrder(){
         $this->load->model('s/Order_model');
         $this->Order_model->userid=$this->userid;
         $result=$this->Order_model->buyOrderList();
         if(!$result){
             $result='';
         }
         if($result){
             Often::Output($this->config->item('trues'),'','',$result);
         }else {
             Often::Output($this->config->item('false'),$this->config->item('term_msg'),'','');
         }
     }
     /**
      * @abstract 支付功能
      */
     function wxPayShop(){
         
     }
     /**
      * @abstract 通过用户的useid 查找发布商品的id,
      */
     function sellOrder(){
         $re_use=array();
         //通过userid 查找该用户所发布的商品编号
         $this->load->model('b/Busine_model');
         $this->Busine_model->userid=$this->userid;
         $busineshopres=$this->Busine_model->getUserShop();
         
         //把用户发布的产品id 用逗号拼接
         $re_use=explode(array_column($busineshopres,'busine_id'));
         var_dump($re_use);
         return $re_use;
     }
   /* function orderLists(){
        $state=$this->input->get('state',true);
        $result=$this->getOrderList();
        $stateifno=$this->config->item('order_option');
        $this->assign('result',$result);
        $this->assign('page','');
        $this->assign('state',$state);
        $this->assign('stateinfo', $stateifno);
        $this->display('s/slist.html');
    }
    function  ajaxrderList(){
        $result=$this->getOrderList();
        Often::Output($result);
    }
    function  getOrderList(){
        //校验需要读取的订单状态
        $state=$this->input->get('state',true);
        empty($state) ? $state = 'all' : '';
        $configstate=array('all','pay','confim','deal','cancel','pending');
        if(!in_array($state, $configstate)){
            Often::Output($this->config->item('false'));
        }
        //校验分页参数
        $page=$this->input->get('page',true);
        empty($page) ? $page = 1 : '';
        if(!is_numeric($page) || $page < 0){
            Often::Output($this->config->item('false'));
        }
        $this->load->model('s/Order_model');
        $this->Order_model->state=$state;
        $this->Order_model->page=$page;
        $this->Order_model->userid=$this->userid;
        $result=$this->Order_model->orderLists();
        if(!$result){
            return '';
        }
        return $result;
    } */
    /**
     * 查看订单
     */
    function orderinfo(){
        $orderId=$this->input->get('orderId',true);
        if(empty($orderId) || !is_numeric($orderId) || $orderId < 0){
            Often::Output($this->config->item('false'),$this->config->item('term_msg'),'','');
        }
        //读取订单详细内容
        $this->load->model('s/Order_model');
        $this->Order_model->number=$orderId;
        $this->Order_model->userid=$this->userid;
        $result=$this->Order_model->orderInfo();
        if(!$result){
            Often::Output($this->config->item('false'),'','','');
        }
        //读取默认地址
        $this->load->model('s/Address_model');
        $this->Address_model->userid = $this->userid;
        $this->Address_model->addrId = $result['addrid'];
        $address=$this->Address_model->selectOneAdd();
        if(!$address){
            $address='';
        }
        $result['address']=$address;
        $this->assign('result',$result);
        $this->display('s/orderDet.html');
    }
    
    /**********************订单模块*************/
    /**
     * 地址管理
     */
    function addreslists(){
	  $this->userid=$this->userid;
	  //获取从订单进来的标示
	  $state=$this->input->get('state',true); 
	  $num=$this->input->get('num',true);
	  if(isset($state) && is_string($state)){
	     if($num){
	         $state=$state.'&num='.$num;
	      }
		 $this->assign('state',$state);
	  }else{
		$this->assign('state','');
	  }
      $this->load->model('s/Shop_model');
      $this->Shop_model->userid=$this->userid;
      $result=$this->Shop_model->addressList();
      if(!$result){
          $result='';
      }
      $this->assign('result',$result);
      $this->display('s/addr.html');
    }
    
    /**
     * 通过id 查看地址
     */
     function selectOneAdd(){
        $this->id=1;
    	$this->load->model('s/Shop_model');
        $this->Shop_model->id=$this->id;
        $result=$this->Shop_model->selectOneAdd();
        if($result){
            Often::Output($this->config->item('trues'),'','',$result);
        }else{
            Often::Output($this->config->item('false'),$this->config->item('term_msg'),'lists','');
        }
    }
    /**
     * 检查添加地址的参数
     */
     function checkaddAddress(){
 	     $data=$this->input->post();
 	     //校验状态
 	     if(!isset($data['sign']) || empty($data['sign']) && !is_string($data['sign'])){
 	       Often::Output($this->config->item('false'),$this->config->item('term_msg'));
 	     }
        $newData['sign'] = $data['sign'];
         //校验名字
        if(empty($data['name']) && preg_match('/^[\x{4e00}-\x{9fa5}]+$/u', $data['name']) < 0){
            Often::Output($this->config->item('false'),$this->config->item('term_msg'));
        }
        $newData['name'] = $data['name'];
        //效验手机号码
        if(!preg_match("/^1[34578]{1}\\d{9}$/",$data['phone'])){
	          Often::Output($this->config->item('false'),$this->config->item('term_msg'));
	      }
	     $newData['phone'] = $data['phone'];
	     //效验省份
	     if(empty($data['province']) && preg_match('/^[\x{4e00}-\x{9fa5}]+$/u', $data['province']) < 0){
             Often::Output($this->config->item('false'),$this->config->item('term_msg'));
         }
         $newData['province'] = $data['province'];
         //效验市区
	     if(empty($data['city']) && preg_match('/^[\x{4e00}-\x{9fa5}]+$/u', $data['city']) < 0){
             Often::Output($this->config->item('false'),$this->config->item('term_msg'));
         }
         $newData['city'] = $data['city'];
         //效验县区
	     if(empty($data['area']) && preg_match('/^[\x{4e00}-\x{9fa5}]+$/u', $data['area']) < 0){
             Often::Output($this->config->item('false'),$this->config->item('term_msg'));
         }
         $newData['area'] = $data['area'];
         //效验详细地址
	     if(empty($data['detail'])){
             Often::Output($this->config->item('false'),$this->config->item('term_msg'));
         }
         $newData['details'] = $data['detail'];
         return $newData;
     }
     
     /**
      * 通过id 或者状态 进入到用户添加/修改地址页面
      */
      function intoAddressAdd(){
        $data=$this->input->get();
        if($data){
            //$data['state] //从订单进来的 
            if(!isset($data['state']) || empty($data['state']) || !is_string($data['state']) || $data['state']==null){
                $this->assign('state','');
            }else if(isset($data['state']) && $data['state']!='' && is_string($data['state'])){
                if(isset($data['num']) && $data['num'] && $data['num']>0 && is_numeric($data['num'])){
                    $num=$data['num'];
                    $state=$data['state'].'&num='.$num;
                }else{
                    $state=$data['state'];
                }
                $this->assign('state',$state);
            }
            //$data['sign] //add 添加 edit 修改
            if(empty($data['sign']) || !is_string($data['sign']) || !isset($data['sign'])){
                Often::Output($this->config->item('false'),$this->config->item('term_msg'));
            }
            //验证获取的参数 是否有id 如果有id 就是点击编辑进入该页面  没有id 就是添加操作
            if(!isset($data['id'])){
                $result='';
            }else if(isset($data['id']) && $data['id']!='' && $data['id']>0){
                $this->load->model('s/Address_model');
                $this->Address_model->addrId=$data['id'];
                $this->Address_model->userid=$this->userid;
                $result=$this->Address_model->selectOneAdd();
                $result['sign']=$data['sign'];
            } 
            $this->assign('result',$result);
        }else{
            $data['sign']=1;
        }
        $this->assign('sign',$data['sign']);
      	$this->display('s/addaddr.html');
      }
    /**
     * 添加地址
     */
    function  addaddres(){
        $newData=$this->checkaddAddress();
        if($newData){
        	$this->load->model('s/Shop_model');
        	$this->Shop_model->userid=$this->userid;
    			$this->Shop_model->name=$newData['name'];
    			$this->Shop_model->phone=$newData['phone'];
    			$this->Shop_model->province=$newData['province'];
    			$this->Shop_model->city=$newData['city'];
    			$this->Shop_model->area=$newData['area'];
    			$this->Shop_model->details=$newData['details'];
        	$res=$this->Shop_model->addAddress();
        	if($res){
        		Often::Output($this->config->item('trues'),'','addreslists');
        	}else{
        		Often::Output($this->config->item('false'),$this->config->item('term_msg'));	
        	}
        }else{
			 Often::Output($this->config->item('false'),$this->config->item('term_msg'));        	
      }
    }
    /**
     * 检查修改地址参数
     */
    function checkDateAddress(){
       $data=$this->input->post();
      //验证当前的状态是否是添加状态
      if(isset($data['sign']) && $data['sign']!='default' && $data['sign']!='del'){
    	    $newData=$this->checkaddAddress();
      }
      if(isset($data['sign']) && ($data['sign']=='del' || $data['sign']=='default')){
           $newData['sign']=$data['sign'];
      }
      if(!isset($data['id']) || empty($data['id']) || !is_numeric($data['id']) || $data['id']<0){
          Often::Output($this->config->item('false'),$this->config->item('term_msg'));
      }
      $newData['id']=$data['id'];
      if(isset($data['sign']) && $data['sign']!='default'){
          if(!isset($data['state']) || empty($data['state']) || !is_string($data['state']) ){
              Often::Output($this->config->item('false'),$this->config->item('term_msg'));
          }
          $newData['state']=$data['state'];
      }
      return $newData;
    }
    /**
     * 修改/删除地址
     */
    function  updateAddress(){
        $newDatas=$this->checkDateAddress();
		$this->load->model('s/Address_model');
		$this->Address_model->addid=$newDatas['id'];
		if($newDatas){
  		    switch($newDatas['sign']){
  		 		case 'del':
  		 		//假删数据
  		 			 $this->Address_model->status=-1;
  		 			break;
  		 		case 'edit':
  		 		//修改地址
  		 			$this->Address_model->name=$newDatas['name'];
  		 			$this->Address_model->phone=$newDatas['phone'];
  		 			$this->Address_model->province=$newDatas['province'];
  		 			$this->Address_model->city=$newDatas['city'];
  		 			$this->Address_model->area=$newDatas['area'];
  		 			$this->Address_model->details=$newDatas['details'];
  		 			break;
  		 		case 'default':
  		 		//修改为默认地址
  		 			$this->Address_model->status=2;
  		 			break;
  		 		default:;
  		 		  break;
  		}
	   $this->Address_model->userid=$this->userid;
       $this->Address_model->sign=$newDatas['sign'];
       $result=$this->Address_model->updateAddress();
       if($result){
          Often::Output($this->config->item('trues'),'','addreslists');
       }else{
       	Often::Output($this->config->item('false'),$this->config->item('term_msg'));
       }
		 }else{
	         Often::Output($this->config->item('false'),$this->config->item('term_msg'));
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
     /**
     * @abstract 导入商品
     */
    function importGoods(){
        //判断文件是否存在
        $file='htmlinfo_YD.log';
        if(!file_exists($file)){
            echo '文件不存在';
            exit();
        };   
        //打开文件     
        $file= fopen($file,"r"); 
        $resp=array();
        while (!feof($file)){
            $line = fgets($file);
            $data=explode(';', $line);
            $info=array();
            foreach ($data as $key=>$val){
                     if(strpos($val,'=')){
                         $temp=explode('=',$val);
                         $k=$temp['0'];
                         $v=$temp['1'];
                         if($k == 'param'){  
                             $info[$k]=str_replace($k.'=', '', $val);
                         }else{
                             $info[$k]=$v;
                         }  
                     }
            }
            if(!empty($info)){
                $resp[]=$info;
            }
        }
        fclose($file);
        $content='';
        foreach ($resp as $key=>$val){
            if(!isset($val['price'])){
                $price=0;
            }else{
                $price=$val['price']*100;
            }
                 $content.= "('"
                        .$val['titles']."','"
                        .$val['param']."','"
                        .$val['imgURL']."','"
                        .$val['desUrl']."','"
                        .$val['source']."','"
                        .json_encode(array('price'=>str_replace('.00', '', $price),'credit'=>$val['credit']))."'),";
                //file_put_contents('yd.txt', $content."\r\n",FILE_APPEND);
           
        }
        
        //$this->load->database();
        $sql='insert into  conv_goods (goods_name,goods_content,goods_img,goods_picture,goods_source,goods_source_info) values'.trim($content,',');
        file_put_contents('zs.txt', $sql."\r\n",FILE_APPEND);
        //$res=$this->db->query($sql); 
        //var_dump($res);
    } 
}