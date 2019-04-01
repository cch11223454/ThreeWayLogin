<?php

class github{

	private $client_id;
	private $callback;
	private $client_secret;
	private $headers=null;

	function __construct($config){
		
		$this->client_id=$config['client_id'];
		$this->client_secret=$config['client_secret'];
		$this->callback=$config['call_back'];
	}

	public function get_code(){
		$url = "https://github.com/login/oauth/authorize";
		//$config_params = \Yii::$app->params;
		
		$params = [
			'client_id' =>$this->client_id ,
			'redirect_uri' => $this->callback,
		];
		$url= $url."?".http_build_query( $params );
		header("location:{$url}");
	}

	public function callback(){
		$token=$this->getAccessToken();
		$result=$this->getUserInfo($token);
		return $result;
		//dump($result);
	}

 /**
 * 获取access_token
 */

	private function getAccessToken(){
		$postData = [
			'client_id'=>$this->client_id,
			'client_secret'=>$this->client_secret,
			'code'=>$_GET['code'],
		];
		//获取字符串
		//$res = curl ('https://github.com/login/oauth/access_token',$postData);
		//获取json数据
		$res = $this->curl ('https://github.com/login/oauth/access_token',$postData,['Accept: application/json']);
		//xml数据
		//$res = curl ('https://github.com/login/oauth/access_token',$postData,['Accept: application/xml']);
		$res = json_decode ($res,true);
		return $res['access_token'];
	}
/**
 * 根据access_token换取用户信息
 */
	private function getUserInfo($access_token){
		$res = $this->curl ('https://api.github.com/user?access_token='.$access_token,[],['User-Agent:cch11223454']);
		$res = json_decode ($res,true);
		return $res;
	
	}

	/**
	 * curl 请求
	 * @param       $url		请求地址
	 * @param array $postData	请求数据
	 * @param array $headers	头信息
	 *
	 * @return string
	 */
	function curl($url,$postData = [],$headers = []){
		$ch = curl_init ();
		curl_setopt ($ch,CURLOPT_URL,$url);
		curl_setopt ($ch,CURLOPT_HEADER,0);
		curl_setopt ($ch,CURLOPT_RETURNTRANSFER,1);
		curl_setopt ($ch,CURLOPT_SSL_VERIFYPEER,false);
		curl_setopt ($ch,CURLOPT_SSL_VERIFYHOST,false);
		curl_setopt ($ch,CURLOPT_HTTPHEADER,$headers);
		if($postData){
			curl_setopt ($ch,CURLOPT_TIMEOUT,60);
			curl_setopt ($ch,CURLOPT_POST,1);
			curl_setopt ($ch,CURLOPT_POSTFIELDS,$postData);
		}
		if(curl_exec ($ch) == false){
			$data = '';
		}else{
			$data = curl_multi_getcontent ($ch);
		}
		curl_close ($ch);
		return $data;
	}
}