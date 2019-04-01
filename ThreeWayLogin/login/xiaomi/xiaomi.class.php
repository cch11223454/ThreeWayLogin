<?php

require_once dirname(__FILE__).'/utils/XMUtil.php';
require_once dirname(__FILE__).'/utils/AccessToken.php';
require_once dirname(__FILE__).'/httpclient/XMHttpClient.php';
require_once dirname(__FILE__).'/httpclient/XMOAuthClient.php';
require_once dirname(__FILE__).'/httpclient/XMApiClient.php';

class xiaomi {

	private $clientId;
	private $clientSecret;
	private $redirectHost;
	private $call_back;
	private $userProfilePath = '/user/profile'; 

	function __construct($config){

		$this->clientId=$config['app_id'];
		$this->clientSecret=$config['app_secret'];
		//$this->redirectHost=$config['call_back'];
		$this->call_back=$config['call_back'];

	}

	function get_code(){
		$responseType = 'code';
		//$redirectUri = $redirectHost."/xiaomilogin/getToken.php";
		$oauthClient = new XMOAuthClient($this->clientId, $this->clientSecret );

		$oauthClient->setRedirectUri($this->call_back);
		//dump($oauthClient);exit;
		$state = 'state';
		$url = $oauthClient->getAuthorizeUrl($responseType, $state);
		//dump($url);exit;
		Header("HTTP/1.1 302 Found");
		Header("Location: $url");
	}

	function callback(){
		$code = $_GET["code"];
		if(!$code){
			print "Get code error : ".  $_GET["error"]. "  error description : ".  $_GET["error_description"];
			exit;
		}
		$oauthClient = new XMOAuthClient($this->clientId, $this->clientSecret );
	    $oauthClient->setRedirectUri($this->call_back);
	    $token = $oauthClient->getAccessTokenByAuthorizationCode($code);
	    //dump($token);exit;
	    if($token) {
	        // 如果有错误，可以获取错误号码和错误描述
	        if  ($token->isError()) {
	            $errorNo = $token->getError();
	            $errordes = $token->getErrorDescription();
	            print "error no : ".$errorNo. "   error description : ".$errordes."<br>";
	        } else {
	            // mac access type
	            //  token有较长的有效期，可以存储下来，不必每次去获取token
	            //var_dump($token);
	            // 拿到token id
	            $tokenId = $token->getAccessTokenId();
	            //dump($tokenId);exit;

	            // 创建api client
	            $xmApiClient = new XMApiClient($this->clientId, $tokenId);
	            //dump($xmApiClient);exit;
	            // 获取nonce  随机数:分钟
	            $nonce = XMUtil::getNonce();

	            $path = $userProfilePath;
	            $method = "GET";
	            $params = array('token' => $tokenId, "clientId" => $this->clientId);
	             
	            // 计算签名
	            $sign = XMUtil::buildSignature($nonce, $method,  $xmApiClient->getApiHost(), $path, $params, $token->getMacKey());

	            // 构建header
	            $head =XMUtil::buildMacRequestHead($tokenId, $nonce, $sign);
	            // 访问api
	            $result = $xmApiClient->callApi($this->userProfilePath, $params, false, $head);
	            //$result = $xmApiClient->callApiSelfSign($this->userProfilePath, array(), $token->getMacKey());
	            // 返回json
	            //dump($result);exit;
	            //print '<br><br>';
	            //var_dump($result);
	            //print '<br><br>';
	            return $result['data'];
	            // 返回json
	           // var_dump($result);
	        }
	    }else {
	        print "Get token Error";
	    }
	}


}
?>