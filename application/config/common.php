<?php
/**********微信配置****************/
$config['appid']='wx780b312e2fc3d66f';
$config['appsecret']='0400452cd20ab7aac14f080f17259cb4'; 

/**************配置信息提示***************************/
$config['trues']                             =  30010;
$config['false']                            =  30011;
$config['term_msg']                         =  '非法参数请求';
/**************商品来源***************************/
$config['source_zs']                        =  10011;//招商
$config['source_jt']                        =  10012;//交通
$config['source_gd']                        =  10013;//光大
$config['source_yd']                        =  10014;//移动

/***************第三方请求接口地址所需账号资料************************/

$config['jindian_appid']                     ='k6XgEPRn7VEC7NDs';
$config['jindian_secret']                    ='NgpuTEXwUHokkSdlG3b8kRsrWc0Gu1beznIWcK9N45G1buECCGzAKQ3xh5IB008q';

/********************订单状态************************/
$config['order_state']                       =array(
        '1'=>'待支付', '2'=>'已支付', '3'=>'已发货', '4'=>'已取消', '10'=>'已完成',
);
$config['order_option']                       =array(
        'all'=>'全部', 'pay'=>'待付款', 'pending'=>'待发货','confim'=>'待收货', 'deal'=>'已成交', 'cancel'=>'已取消',
);
$config['order_stateinfo']                   =array(
        '1'=>array('name'=>'去支付','url'=>''), 
        '2'=>array('name'=>'查看订单','url'=>''), 
        '3'=>array('name'=>'查看订单','url'=>''),
        '4'=>array('name'=>'查看订单','url'=>''),
        '10'=>array('name'=>'查看订单','url'=>'')
);
$config['jindian_api_sso']                  = 'http://106.38.36.98:9080/sso/index.html';//登录页面
$config['jindian_api_token']                = 'http://106.38.36.98:9080/sso/token.do';//校验令牌是否有效
/***************系统免登陆验证的地址************************/
$config['no_login']                         =array(
        '1'=>'/i/init/ipage',
        '2'=>'/s/init/lists',
        '3'=>'/h/init/lists',
        '4'=>'/s/init/shopinfo',
        '5'=>'/h/init/findsearch',
        '6'=>'/h/init/goodsinfo',
        '7'=>'/s/init/Shopsear',
        '8'=>'/s/init/ajaxlists',
        '9'=>'/s/init/ajaxlists',
        '10'=>'/h/init/ajaxlists',
		'11'=>'/h/init/getTypeName',
        '12'=>'/h/init/getTypeSmallName',
        '13'=>'/h/init/getGoodClass',
		'14'=>'/s/init/getTypeName',
        '15'=>'/s/init/getTypeSmallName',
        '16'=>'/s/init/getGoodClass',
        '17'=>'/i/init/ajaxlists',
        '18'=>'/t/init/shareHtml',
        '19'=>'/t/Init/checkRegisUse',
        '20'=>'/t/Init/authUser',
        '21'=>'/t/init/shareInsUserTask'
);//'3'=>'s/init/shopinfo',商城产品详情需要读取购物车数据
/***************商城和回收模块 固定的热门搜索关键词************************/
$config['hot_search']                         =array(
    array('hosName'=>'微波炉'),
    array('hosName'=>'美的'),
    array('hosName'=>'飞科'),
    array('hosName'=>'九阳')
);
/***************任务中心模块 被邀请的新注册用户获取的奖励(冻结不可用,认证后才可以使用)************************/
$config['task_newReg'] =50;