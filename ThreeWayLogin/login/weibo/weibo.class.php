<?php

require dirname(__FILE__).'/saetv2.ex.class.php';
class weibo{

	public $app_key;
	public $app_secret;
	public $call_back;

	function __construct($config){
		
		$this->app_key=$config['app_key'];
		$this->app_secret=$config['app_secret'];
		$this->call_back=$config['call_back'];
	}
	
	public function get_code(){
		$o = new \SaeTOAuthV2( $this->app_key,$this->app_secret);
		//dump($o);exit;
		$code_url = $o->getAuthorizeURL($this->call_back);
		header("Location:{$code_url}");	
	}

	function callback(){
		$access_token=$this->get_access_token($_REQUEST['code']);
		$user_info=$this->get_user_info($access_token);
		//var_dump($user_info);
		return $user_info;
	}

	public function get_access_token($code){
		$b = new \SaeTOAuthV2( $this->app_key,$this->app_secret);
		$keys = array();
		$keys['code'] =$code;
		$keys['redirect_uri'] =$this->call_back;
		$token = $b->getAccessToken( 'code', $keys ) ;
		return $token['access_token'];

	}

	public function get_user_info($access_token){
		$c = new \SaeTClientV2($this->app_key, $this->app_secret,$access_token);
		$ms  = $c->home_timeline(); // done
		$uid_get = $c->get_uid();
		$uid = $uid_get['uid'];
		$user_info = $c->show_user_by_id($uid);//根据ID获取用户等基本信息

		return $user_info;
	}


}

?>