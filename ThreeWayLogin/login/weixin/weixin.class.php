<?php
class weixin{
	// private $client_id;
	// private $callback;
	// private $client_secret;
	private $AppID;
	private $AppSecret;
	private $callback; //回调地址

	function __construct($config){
		
		$this->AppID=$config['app_id'];
		$this->AppSecret=$config['app_secret'];
		$this->callback=$config['call_back'];
	}

	public function get_code(){
		$state  = md5(uniqid(rand(), TRUE));
		//$_SESSION["wx_state"]    =   $state; //存到SESSION
		$callback = urlencode($this->callback);
		$wxurl = "https://open.weixin.qq.com/connect/qrconnect?appid=".$this->AppID."&redirect_uri={$this->callback}&response_type=code&scope=snsapi_login&state={$state}#wechat_redirect";
		header("Location: $wxurl");
	}

	public function callback(){
		
		$url='https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$this->AppID.'&secret='.$this->AppSecret.'&code='.$_GET['code'].'&grant_type=authorization_code';
		$arr=$this->http_get($url);
		$arr=json_decode($arr,1); //得到 access_token 与 openid
		

		$url='https://api.weixin.qq.com/sns/userinfo?access_token='.$arr['access_token'].'&openid='.$arr['openid'].'&lang=zh_CN';
		$result=$this->http_get($url);
		$result=json_decode($result,1);
		return $result;
	}

	private function http_get($url){
	    $oCurl = curl_init();
	    if(stripos($url,"https://")!==FALSE){
	        curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
	        curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, FALSE);
	        curl_setopt($oCurl, CURLOPT_SSLVERSION, 1);
	    }
	    curl_setopt($oCurl, CURLOPT_URL, $url);
	    curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1 );
	    $sContent = curl_exec($oCurl);
	    $aStatus = curl_getinfo($oCurl);
	    curl_close($oCurl);
	    if(intval($aStatus["http_code"])==200){
	        return $sContent;
	    }else{
	        return false;
	    }
	}


	private http_post($url,$param,$post_file=false){
	    $oCurl = curl_init();
	    if(stripos($url,"https://") !== FALSE){
	        curl_setopt($oCurl,CURLOPT_SSL_VERIFYPEER,FALSE);
	        curl_setopt($oCurl,CURLOPT_SSL_VERIFYHOST,false);
	        curl_setopt($oCurl,CURLOPT_SSLVERSION,1);
	    }
	    if (is_string($param) || $post_file){
	        $strPOST = $param;
	    } else {
	        $aPOST = array();
	        foreach($param as $key => $val){
	            $aPOST[] = $key."=" . urlencode($val);
	        }
	        $strPOST = join("&",$aPOST);
	    }
	    curl_setopt($oCurl,CURLOPT_URL,$url);
	    curl_setopt($oCurl,CURLOPT_RETURNTRANSFER,1);
	    curl_setopt($oCurl,CURLOPT_POST,true);
	    curl_setopt($oCurl,CURLOPT_POSTFIELDS,$strPOST);
	    $sContent = curl_exec($oCurl);
	    $aStatus = curl_getinfo($oCurl);
	    curl_close($oCurl);
	    if(intval($aStatus["http_code"]) == 200){
	        return $sContent;
	    }else{
	        return false;
	    }
	}
}

?>