<?php

class taobao {

	private $app_key;
	private $app_secret;
	private $headers=null;
	private $callback;

	function __construct($config){

		$this->app_key=$config['app_key'];
		$this->app_secret=$config['app_secret'];
		$this->callback=$config['call_back'];
	}

	function get_code(){
		$url = "https://oauth.taobao.com/authorize";
		//$config_params = \Yii::$app->params;
		
		
		$params = [
			'client_id' =>$this->app_key ,
			'redirect_uri' => $this->callback,
			'response_type'=>"code",
		];
		$url= $url."?".http_build_query( $params );
		header("location:{$url}");
	}

	function callback(){
		$result=$this->get_user_info();
		return $result;
	}

	function get_user_info(){
		$code=$_GET['code'];
		

		$url="https://oauth.taobao.com/token?grant_type=authorization_code&response_type=code&client_id=".$this->app_key."&client_secret=".$this->app_secret."&redirect_uri=".$this->callback."&code={$code}";
	
		//$url="https://oauth.taobao.com/";
		$result=$this->http_post($url);
		$result=get_object_vars(json_decode($result));
		$result['taobao_user_nick']=urldecode($result['taobao_user_nick']);
		return $result;
		//dump($result);


	}


	function http_post($url,$param=[],$post_file=false){
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