<?php 
 
class qq {


 private $app_id='';
 private $app_key='';
 private $call_back='';

 private $scope='get_user_info,list_album,add_album,upload_pic,add_topic,add_weibo';

 const GET_AUTH_CODE_URL = "https://graph.qq.com/oauth2.0/authorize"; 
 const GET_ACCESS_TOKEN_URL = "https://graph.qq.com/oauth2.0/token"; 
 const GET_OPENID_URL = "https://graph.qq.com/oauth2.0/me"; 
 private $APIMap = array( 
  "get_user_info" => array(   //获取用户资料 
   "https://graph.qq.com/user/get_user_info", 
   array("format" => "json"), 
  ), 
  "add_t" => array(    //发布一条普通微博 
   "https://graph.qq.com/t/add_t", 
   array("format" => "json", "content","#clientip","#longitude","#latitude","#compatibleflag"), 
   "POST"
  ), 
  "add_pic_t" => array(    //发布一条图片微博 
   "https://graph.qq.com/t/add_pic_t", 
   array("content", "pic", "format" => "json", "#clientip", "#longitude", "#latitude", "#syncflag", "#compatiblefalg"), 
   "POST"
  ), 
  "del_t" => array(      //删除一条微博 
   "https://graph.qq.com/t/del_t", 
   array("id", "format" => "json"), 
   "POST"
  ), 
  "get_repost_list" => array(    //获取单条微博的转发或点评列表 
   "https://graph.qq.com/t/get_repost_list", 
   array("flag", "rootid", "pageflag", "pagetime", "reqnum", "twitterid", "format" => "json") 
  ), 
  "get_info" => array(     //获取当前用户资料 
   "https://graph.qq.com/user/get_info", 
   array("format" => "json") 
  ), 
  "get_other_info" => array(    //获取其他用户资料 
   "https://graph.qq.com/user/get_other_info", 
   array("format" => "json", "#name-1", "#fopenid-1") 
  ), 
  "get_fanslist" => array( 
   "https://graph.qq.com/relation/get_fanslist", //我的微博粉丝列表 
   array("format" => "json", "reqnum", "startindex", "#mode", "#install", "#sex") 
  ), 
  "get_idollist" => array( 
   "https://graph.qq.com/relation/get_idollist", //我的微博收听列表 
   array("format" => "json", "reqnum", "startindex", "#mode", "#install") 
  ), 
  "add_idol" => array( 
   "https://graph.qq.com/relation/add_idol",  //微博收听某用户 
   array("format" => "json", "#name-1", "#fopenids-1"), 
   "POST"
  ), 
  "del_idol" => array(   //微博取消收听某用户 
   "https://graph.qq.com/relation/del_idol", 
   array("format" => "json", "#name-1", "#fopenid-1"), 
   "POST"
  ) 
 ); 
 private $keysArr; 
 function __construct($config,$access_token="",$openid = ""){
 
  $this->app_id=$config['app_id'];
  $this->app_key=$config['app_key'];
  $this->call_back=$config['call_back'];
  
  if($access_token==="" || $openid === ""){ 
   $this->keysArr = array( 
    "oauth_consumer_key" => $this->app_id 
     
   ); 
  }else{ 
   $this->keysArr = array( 
    "oauth_consumer_key" => $this->app_id,
    "access_token" => $access_token, 
    "openid" => $openid 
   ); 
  } 
 } 
 public function get_code(){ 
  //-------生成唯一随机串防CSRF攻击 
  $_SESSION['state'] = md5(uniqid(rand(), TRUE)); 
  $keysArr = array( 
   "response_type" => "code", 
   "client_id" => $this->app_id, 
   "redirect_uri" => $this->call_back, 
   "state" => $_SESSION['state'], 
   "scope" => $this->scope, 
  ); 
  $login_url = self::GET_AUTH_CODE_URL.'?'.http_build_query($keysArr); 
  header("Location:$login_url"); 
 }


 public function qq_callback(){ 
  //--------验证state防止CSRF攻击 
  if($_GET['state'] != $_SESSION['state']){ 
   return false; 
  } 
  //-------请求参数列表 
  $keysArr = array( 
   "grant_type" => "authorization_code", 
   "client_id" => $this->app_id, 
   "redirect_uri" => $this->call_back, 
   "client_secret" => $this->app_key, 
   "code" => $_GET['code'] 
  ); 
  //------构造请求access_token的url 
  $token_url = self::GET_ACCESS_TOKEN_URL.'?'.http_build_query($keysArr); 
  $response = $this->get_contents($token_url); 
  if(strpos($response, "callback") !== false){ 
   $lpos = strpos($response, "("); 
   $rpos = strrpos($response, ")"); 
   $response = substr($response, $lpos + 1, $rpos - $lpos -1); 
   $msg = json_decode($response); 
   if(isset($msg->error)){ 
    $this->showError($msg->error, $msg->error_description); 
   } 
  } 
  $params = array(); 
  parse_str($response, $params); 
  $_SESSION["access_token"]=$params["access_token"]; 
  $this->keysArr['access_token']=$params['access_token']; 
  return $params["access_token"]; 
 } 
  
 public function get_contents($url){ 
  if (ini_get("allow_url_fopen") == "1") { 
   $response = file_get_contents($url); 
  }else{ 
   $ch = curl_init(); 
   curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE); 
   curl_setopt($ch, CURLOPT_URL, $url); 
   $response = curl_exec($ch); 
   curl_close($ch); 
  } 
  if(empty($response)){ 
   return false; 
  } 
  return $response; 
 } 
 
 public function get_openid(){ 
  //-------请求参数列表 
  $keysArr = array( 
   "access_token" => $_SESSION["access_token"] 
  ); 
  $graph_url = self::GET_OPENID_URL.'?'.http_build_query($keysArr); 
  $response = $this->get_contents($graph_url); 
  //--------检测错误是否发生 
  if(strpos($response, "callback") !== false){ 
   $lpos = strpos($response, "("); 
   $rpos = strrpos($response, ")"); 
   $response = substr($response, $lpos + 1, $rpos - $lpos -1); 
  } 
  $user = json_decode($response); 
  if(isset($user->error)){ 
   $this->showError($user->error, $user->error_description); 
  } 
  //------记录openid 
  $_SESSION['openid']=$user->openid; 
  $this->keysArr['openid']=$user->openid; 
  return $user->openid; 
 } 
  
 /** 
  * showError 
  * 显示错误信息 
  * @param int $code 错误代码 
  * @param string $description 描述信息（可选） 
  */
 public function showError($code, $description = '$'){ 
   echo "<meta charset=\"UTF-8\">"; 
   echo "<h3>error:</h3>$code"; 
   echo "<h3>msg :</h3>$description"; 
   exit(); 
 } 
  
 /** 
  * _call 
  * 魔术方法，做api调用转发 
  * @param string $name 调用的方法名称 
  * @param array $arg  参数列表数组 
  * @since 5.0 
  * @return array   返加调用结果数组 
  */
 public function __call($name,$arg){ 
  //如果APIMap不存在相应的api 
  if(empty($this->APIMap[$name])){ 
   $this->showError("api调用名称错误","不存在的API: <span style='color:red;'>$name</span>"); 
  } 
  //从APIMap获取api相应参数 
  $baseUrl = $this->APIMap[$name][0]; 
  $argsList = $this->APIMap[$name][1]; 
  $method = isset($this->APIMap[$name][2]) ? $this->APIMap[$name][2] : "GET"; 
  if(empty($arg)){ 
   $arg[0] = null; 
  } 
  $responseArr = json_decode($this->_applyAPI($arg[0], $argsList, $baseUrl, $method),true); 
  //检查返回ret判断api是否成功调用 
  if($responseArr['ret'] == 0){ 
   return $responseArr; 
  }else{ 
   $this->showError($responseArr['ret'], $responseArr['msg']); 
  } 
 } 
  
 //调用相应api 
 private function _applyAPI($arr, $argsList, $baseUrl, $method){ 
  $pre = "#"; 
  $keysArr = $this->keysArr; 
  $optionArgList = array();//一些多项选填参数必选一的情形 
  foreach($argsList as $key => $val){ 
   $tmpKey = $key; 
   $tmpVal = $val; 
   if(!is_string($key)){ 
    $tmpKey = $val; 
    if(strpos($val,$pre) === 0){ 
     $tmpVal = $pre; 
     $tmpKey = substr($tmpKey,1); 
     if(preg_match("/-(\d$)/", $tmpKey, $res)){ 
      $tmpKey = str_replace($res[0], "", $tmpKey); 
      $optionArgList[]= $tmpKey; 
     } 
    }else{ 
     $tmpVal = null; 
    } 
   } 
   //-----如果没有设置相应的参数 
   if(!isset($arr[$tmpKey]) || $arr[$tmpKey] === ""){ 
    if($tmpVal == $pre){ 
     continue; 
    }else if($tmpVal){//则使用默认的值 
     $arr[$tmpKey] = $tmpVal; 
    }else{ 
     $this->showError("api调用参数错误","未传入参数$tmpKey"); 
    } 
   } 
   $keysArr[$tmpKey] = $arr[$tmpKey]; 
  } 
  //检查选填参数必填一的情形 
  if(count($optionArgList)!=0){ 
   $n = 0; 
   foreach($optionArgList as $val){ 
    if(in_array($val, array_keys($keysArr))){ 
     $n++; 
    } 
   } 
   if(!$n){ 
    $str = implode(",",$optionArgList); 
    $this->showError("api调用参数错误",$str."必填一个"); 
   } 
  } 
  if($method == "POST"){ 
   $response = $this->post($baseUrl, $keysArr, 0); 
  }else if($method == "GET"){ 
   $baseUrl=$baseUrl.'?'.http_build_query($keysArr); 
   $response = $this->get_contents($baseUrl); 
  } 
  return $response; 
 } 
  
 public function post($url, $keysArr, $flag = 0){ 
  $ch = curl_init(); 
  if(! $flag) curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE); 
  curl_setopt($ch, CURLOPT_POST, TRUE); 
  curl_setopt($ch, CURLOPT_POSTFIELDS, $keysArr); 
  curl_setopt($ch, CURLOPT_URL, $url); 
  $ret = curl_exec($ch); 
  curl_close($ch); 
  return $ret; 
 } 
} 