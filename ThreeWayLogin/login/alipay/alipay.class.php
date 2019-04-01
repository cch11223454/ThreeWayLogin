<?php

class alipay {	
	private $appid;
	private $privateKey;
	private $publicKey;
	private $callback;

	public function __construct($config){
		$this->appid=$config['app_id'];
		$this->privateKey=$config['private_Key'];
		$this->publicKey=$config['public_Key'];
		$this->callback=$config['call_back'];
	}

	public function get_code(){
	$url="https://openauth.alipay.com/oauth2/publicAppAuthorize.htm?app_id=".$this->appid."&scope=auth_user&redirect_uri=".$this->callback;
	//echo "<script> top.location.href='" .$url. "'</script>";
	header("location:{$url}");	
	}

	function callback(){
		$arr=$this->get_access_token($_REQUEST["auth_code"]);
	//var_dump($arr);
		$user_info=$this->get_user_info($arr);
		//var_dump($user_info);
		return $user_info;
	}

	public function get_access_token($auth_code){
		require dirname(__FILE__).'/AopClient.php';
		$aop = new \AopClient();
		$aop->gatewayUrl="https://openapi.alipay.com/gateway.do";
		$aop->appId=$this->appid;
		$aop->rsaPrivateKey=$this->privateKey;
		$aop->alipayrsaPublicKey=$this->publicKey;
		$aop->apiVersion='1.0';
		$aop->signType='RSA2';
		$aop->postCharset='utf-8';
		$aop->format='json';

		//根据返回的auth_code换取access_token
		require dirname(__FILE__).'/AlipaySystemOauthTokenRequest.php';
		$request = new \AlipaySystemOauthTokenRequest();
		$request->setGrantType("authorization_code");
		$request->setCode($auth_code);
		$result = $aop->execute($request);
		$access_token = $result->alipay_system_oauth_token_response->access_token;
		$arr=array($access_token,$aop);
		return $arr;
	}

		public function get_user_info($ac_aop){
			//用access_token获取用户信息
			require dirname(__FILE__)."/AlipayUserInfoShareRequest.php";
			$request=new \AlipayUserInfoShareRequest();
			$result = $ac_aop[1]->execute( $request, $ac_aop[0]);
			$responseNode=str_replace(".", "_", $request->getApiMethodName()) . "_response";
			$resultCode=$result->$responseNode->code;
			if(!empty($resultCode) && $resultCode==10000){
				$user_data=$result->$responseNode;
			}
			return $user_data;
		
		}
}

?>