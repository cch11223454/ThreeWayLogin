<?php
require './alilogin/AliLogin.php';
$obj=new \AliLogin();
$obj->appid=""; //appid
$obj->privateKey='';  //应用私钥
$obj->publicKey=''; //支付宝公钥
$obj->callback='';  //回调地址

if(empty($_REQUEST["auth_code"])){
	$obj->get_auth_code();
}

//echo $obj->callback;
if(!empty($_REQUEST["auth_code"])){
	//获取$access_token与AopClient对象
	$arr=$obj->get_access_token($_REQUEST["auth_code"]);
	//var_dump($arr);
	$user_info=$obj->get_user_info($arr);
	var_dump($user_info);

}


?>