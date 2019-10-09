# ThreeWayLogin
```
php第三方登录PC端（qq/微博/微信/github/百度/小米/淘宝/支付宝）

##引入类文件

require "ThreeWayLogin/ThreeWayLogin.php";

//支付宝配置参数

$alipay_config=[
    'app_id'=>"", //appid
    'private_Key'=>'',  //应用私钥
    'public_Key'=>'', //支付宝公钥
    'call_back'=>'https://www.lfveeker.com',  //回调地址
];

$obj= new \ThreeWayLogin('alipay',$alipay_config);

//百度配置参数

$baidu_config=[
        'ak'=>'',  //ak
        'sk'=>'',  //sk
        'call_back'=>'https://www.lfveeker.com',  //回调地址
    ];
    
$obj= new \ThreeWayLogin('baidu',$baidu_config);

//GitHub配置参数

$github_config=[
        'client_id'=>'4f6eb2bca6c1e3e1bb40',  //client_id
        'client_secret'=>'663e8c101b5538c888b2fc7a6712bef521c43f8d', //client_secret
        'call_back'=>'https://www.lfveeker.com',  //回调地址
    ];
    
$obj= new \ThreeWayLogin('github',$github_config);


//腾讯QQ配置参数

$qq_config=[
        'app_id'=>'',   //app_id
        'app_key'=>'',  //app_key
        'call_back'=>'https://www.lfveeker.com', //回调地址
  	];
    
$obj= new \ThreeWayLogin('qq',$qq_config);


//淘宝配置参数

$taobao_config=[
    'app_key'=>'',  //app_key
    'app_secret'=>'',   //app_secret
    'call_back'=>'https://www.lfveeker.com',  //回调地址
];

$obj= new \ThreeWayLogin('taobao',$taobao_config);

//新浪微博配置参数

$weibo_config=[
    'app_key'=>'',  //app_key
    'app_secret'=>'',  //app_secret
    'call_back'=>'https://www.lfveeker.com',  //回调地址
];

$obj= new \ThreeWayLogin('weibo',$weibo_config);


//小米配置参数

$xiaomi_config=[
    'app_id'=>'', //app_id
    'app_key'=>'',  //app_key
    'app_secret'=>'',  //app_secret
    'call_back'=>'https://www.lfveeker.com',  //回调地址
];

$obj= new \ThreeWayLogin('xiaomi',$xiaomi_config);

//微信配置参数

$weixin_config=[
	'app_id' => '',  //app_id
	'app_secret' => '',  //app_secret
	'call_back'  =>  'https://www.lfveeker.com', //回调地址
];

$obj= new \ThreeWayLogin('weixin',$weixin_config);

//调起扫码登录页面

$obj->get_code();

//回调

$obj->callback();
```
