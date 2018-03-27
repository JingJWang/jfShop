<?php
defined('BASEPATH') OR exit('No direct script access allowed');
header("Content-type:text/html;charset=utf-8;");
class init extends RR_Controller{
    /**
     * @abstract 首页-加载搜索页面
     */
    function ipage(){
        //获取分类大类
        $this->load->model('h/Goods_model');
        $typename=$this->Goods_model->getTypeName();
        //获取首页显示数据
        $result=$this->getList();
        $this->assign('result',$result);
        $this->assign('typename',$typename);
        $this->display('i/i.html');
    }
    /**
     *效验首页搜索参数
     *$data['source']  来源筛选
     *$data['type']  分类大类筛选
     *$data['pri']价格区间筛选
     *$data['brand']品牌筛选
     *$data['kind']商品展示 好货 新品 热销
     */
    function checkIndexData(){
        //声明一个空的数组 用来储存效验的数据
        $newData=array(
            'page'=>'',
            'kind'=>'',
        );
        $data=$this->input->post();
        if(isset($data['kind']) && $data['kind']!=''){
            $newData['kind']=$data['kind'];
        }
        if(isset($data['page']) && $data['page']!='' && $data['page']>0){
            $newData['page']=$data['page'];
        }else{
            $newData['page']='0';
        }
        return $newData;
    }
    /**
     * @abstract  购买商品 首页列表
     */
    function  getList(){
        $countData=array();
        $Countpage=0;//总页数
        $this->load->model('i/Shop_model');
        //获取首页搜索ajax提交的参数内容
        $indexData=$this->checkIndexData();
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
              $indexData['page']=1;//结果集小于10 page=1
           }else if($result['res_result']['nums']>=10){
               $Countpage=ceil($result['res_result']['nums']/10);//取总页数
               $indexData['page']=2;//结果集大于10 page=1
           }
        }else{
           $result['result'] = '';
           $result['res_result'] = 0;
           $indexData['page']=0; //没有查询到结果 page=0
        }
        $resue=array(
            'Countpage'=>$Countpage,
            'result'=>$result['result'],
            'page'=>$indexData['page']
        );
       return $resue;
    }
    /**
     * 列表获取方式为ajax
     */
    function  ajaxlists(){
        $result=$this->getList();
        Often::Output($this->config->item('trues'),'','',$result);
    }
}