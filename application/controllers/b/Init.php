<?php
defined('BASEPATH') OR exit('No direct script access allowed');
header("Content-type:text/html;charset=utf-8;");
//商家模块
class init extends RR_Controller{
    /**
     * @abstract 商户查看可以发布商品的页面
     */
    function allShopList(){
        $this->display('busine/issue.html');
    }
    /**
     * @abstract 商户发布商品--效验商品名字
     *$data['name']商品名字筛选
     */
    function checkIndexData(){
        //声明一个空的数组 用来储存效验的数据
        $newData=array(
            'page'=>'',
            'name'=>''
        );
        //$data=array('name'=>'手机','page'=>0);
        $data=$this->input->post();
        //效验商品名字        
        if(isset($data['name']) && $data['name']!='' && !empty($data['name'])){
            $newData['name']=$this->unescape($data['name']);
        }
        //效验页数
        if(isset($data['page']) && $data['page']!='' && $data['page']>0){
            $newData['page']=$data['page'];
        }else{
            $newData['page']='0';
        }
        return $newData;
    }
    /**
     * @abstract 商户发布商品--获取商品类别
     */
    function  getList(){
        $countData=array();
        $Countpage=0;//总页数
        $this->load->model('s/Shop_model');
        //获取首页搜索提交的参数内容
        $indexData=$this->checkIndexData();
        $this->Shop_model->data=$indexData;
        $result=$this->Shop_model->getReleaseShop();
        //处理数组中的json 字符串
        if($result){
           if(!isset($result['res_result']['nums']) || $result['res_result']['nums']<20){
              $result['res_result']['nums']=1;
              $Countpage=$result['res_result']['nums'];//取总页数
           }else if($result['res_result']['nums']>=20){
               $Countpage=ceil($result['res_result']['nums']/20);//取总页数
           }
           $resue=array(
               'Countpage'=>$Countpage,
               //'result'=>array_column($result['result'],'shop_name','shop_id'),
               'result'=>$result['result'],
               'param'=>$indexData
           );
           Often::Output($this->config->item('trues'),'','',$resue);
        }else{
            Often::Output($this->config->item('false'),'获取商品列表失败','','');
        }
    }
    /***************************商户查看发布商品**********************************/
    /**
     * @abstract 商户查看已经发布的商品页面
     */
    function lookReleashTem(){
        $this->display('busine/mygoods.html');
    }
    /**
     * @abstract 商户查看已经发布的商品
     */
    function getReleaseShop(){
        $countData=array();
        $Countpage=0;//总页数
        $this->load->model('b/Busine_model');
        //获取首页搜索提交的参数内容
        $indexData=$this->checkIndexData();
        $this->Busine_model->data=$indexData;
        $result=$this->Busine_model->goodList();
        //处理数组中的json 字符串
        if($result){
            if(!isset($result['res_result']['nums']) || $result['res_result']['nums']<10){
                $result['res_result']['nums']=1;
                $Countpage=$result['res_result']['nums'];//取总页数
            }else if($result['res_result']['nums']>=10){
                $Countpage=ceil($result['res_result']['nums']/10);//取总页数
            }
            $resue=array(
                'Countpage'=>$Countpage,
                'result'=>$result['result'],
                'param'=>$indexData
            );
            Often::Output($this->config->item('trues'),'','',$resue);
        }else{
            Often::Output($this->config->item('false'),'获取商品列表失败','','');
        }
    }
    /**
     * @abstract 效验商品id
     */
    function checkShopId(){
        $shopid=$this->input->post('shopid',true);
        if(!isset($shopid) || !is_numeric($shopid) || $shopid<0){
            Often::Output($this->config->item('false'),$this->config->item('term_msg'));
        }
        return $shopid;
    }
    /**
     * @abstract 效验商品价格和数量
     * 当前默认商户对商品的价格把控权只有一个  钱 1或者金点 2 method=1 or 2
     * num 商品数量
     * method 标识 
     * values 价值
     */
    function checkShopOption(){
        $newData=array(
            'num'=>'',
            'method'=>'',
            'values'=>''
       );
        $arr_method=array(1,2);
        $data=$this->input->post();
        //验证商户填写的商品数量
        if(!isset($data['num']) || !is_numeric($data['num']) || empty($data['num']) || $data['num']<0){
            Often::Output($this->config->item('false'),$this->config->item('term_msg'));
        }
        $newData['num']=$data['num'];
        //验证商户选择商品的价格方式
        if(!in_array($data['method'],$arr_method)){
            Often::Output($this->config->item('false'),$this->config->item('term_msg'));
        }else{
            //验证商户选择商品的价格
            if(!isset($data['values']) || empty($data['values']) || $data['values']<0){
                Often::Output($this->config->item('false'),$this->config->item('term_msg'));
            }
            $newData['values']=$data['values']*100;
        }
        $newData['method']=$data['method'];
        return $newData;
    }
    /**
     * @abstract商户商品 添加需要上架的商品
     * @param userid  int 用户id
     */
    function addBusine(){ 
        //获取商品id
        $shopid=$this->checkShopId();
        //获取商品的价格及数量
        $option=$this->checkShopOption();
        $this->load->model('b/Busine_model');
        $this->Busine_model->userid=$this->userid;
        $this->Busine_model->shopid=$shopid;
        $this->Busine_model->option=$option;
        $result=$this->Busine_model->insert();
        if($result){
            Often::Output($this->config->item('trues'),'','lookReleashTem');
        }else{
            Often::Output($this->config->item('false'),'该产品已经发布了');
        }
    }
    /**
     * @abstract 商户商品--通过商品id查找该商品的信息
     */
    function getShopOneBusine(){
        //获取商品id
        $shopid=$this->checkShopId();
        //$shopid=3;
        $this->load->model('b/Busine_model');
        $this->Busine_model->shopid=$shopid;
        $this->Busine_model->userid=$this->userid;
        $result=$this->Busine_model->getShopOneBusine();
        if($result){
            Often::Output($this->config->item('trues'),'','',$result);
        }else{
            Often::Output($this->config->item('false'),$this->config->item('term_msg'));
        }
    }
    /**
     * @abstract 商户商品--修改商品价格数量
     */
    function updateBusine(){
        //获取商品id
        $shopid=$this->checkShopId();
        //获取商品的价格及数量
        $option=$this->checkShopOption();
        $this->load->model('b/Busine_model');
        $this->Busine_model->userid=$this->userid;
        $this->Busine_model->shopid=$shopid;
        $this->Busine_model->sign=2;//修改商品的参数标志
        $this->Busine_model->option=$option;
        $result=$this->Busine_model->updateBusine();
        if($result){
            Often::Output($this->config->item('trues'),'','lookReleashTem');
        }else{
            Often::Output($this->config->item('false'),$this->config->item('term_msg'));
        }
    }
    /**
     * @abstract 商户商品--修改商品状态
     */
    function checkBusineStatus(){
        $arr_method=array(1,-1);
        $status=$this->input->post('status',true);
        if(!in_array($status,$arr_method)){
            Often::Output($this->config->item('false'),$this->config->item('term_msg'));
        }
        return $status;
    }
    /**
     * @abstract 商户商品--修改商品状态
     */
    function updateBusineStatus(){
        //获取商品id
        $shopid=$this->checkShopId();
        //获取商品的价格及数量
        $status=$this->checkBusineStatus();
        $this->load->model('b/Busine_model');
        $this->Busine_model->userid=$this->userid;
        $this->Busine_model->shopid=$shopid;
        $this->Busine_model->sign=1;//修改商品状态的参数标志
        $this->Busine_model->status=$status;
        $result=$this->Busine_model->updateBusine();
        if($result){
            Often::Output($this->config->item('trues'));
        }else{
            Often::Output($this->config->item('false'),$this->config->item('term_msg'));
        }
    }
    /**
     * @abstract 我的订单 买入或卖出
     */
    function getMyShopList(){
        $this->display('busine/mylist.html');
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