<?php

class ThreeWayLogin {
	private $login_obj;
	private $login_type;

	function __construct($type,$config){		
		$this->login_type=$type;
		include_once __DIR__."/login/{$this->login_type}/{$this->login_type}.class.php";
		$code="\\".$type;
		$this->login_obj=new $code($config);

	}

	function get_code(){
		
		$this->login_obj->get_code();
	}

	function callback(){
		
		if($this->login_type=='weibo'){
			$result=$this->login_obj->callback();
			echo 'id：'.$result['id'].'<br />';
			echo 'screen_name：'.$result['screen_name'].'<br />';
			echo 'name：'.$result['name'].'<br />';
			echo 'location：'.$result['location'].'<br />';
			echo '<img src="'.$result['profile_image_url'].'"/>';
			exit;
		}

		if($this->login_type=='qq'){
			$acs = $this->login_obj->qq_callback();    //access_token 
			$oid=$this->login_obj->get_openid();     //openid 
			$code="\\".$this->login_type;
			$qq_login =new $code($acs,$oid);

			$result = $qq_login->get_user_info();
			$result['openid']=$oid;
			echo 'openid：'.$result['openid'].'<br />';
			echo 'nickname：'.$result['nickname'].'<br />';
			echo 'sex：'.$result['gender'].'<br />';
			echo 'year：'.$result['year'].'<br />';
			echo 'location：'.$result['province'].$result['city'].'<br />';
			echo '<img src="'.$result['figureurl_qq_2'].'"/>';
			exit;
			
		}

		if($this->login_type=='baidu'){			
			$result=$this->login_obj->callback();
			echo 'user_id：'.$result['userid'].'<br />';
			echo 'username：'.$result['username'].'<br />';
			echo 'sex：'.$result['sex'].'<br />';
			echo 'birthday：'.$result['birthday'].'<br />';
			echo '<img src="http://himg.bdimg.com/sys/portrait/item/'.$result['portrait'].'.jpg"/>';
			exit;
		}

		if($this->login_type=='alipay'){
			$result=get_object_vars($this->login_obj->callback());
			echo 'user_id：'.$result['user_id'].'<br />';
			echo 'nick_name：'.$result['nick_name'].'<br />';
			echo 'location：'.$result['province'].$result['city'].'<br />';
			echo '<img src="'.$result['avatar'].'"/>';
			exit;
		}

		if($this->login_type=='xiaomi'){
			$result=$this->login_obj->callback();
			echo 'unionId：'.$result['unionId'].'<br />';
			echo 'miliaoNick：'.$result['miliaoNick'].'<br />';
			echo '<img src="'.$result['miliaoIcon_120'].'"/>';

			exit;
		}

		if($this->login_type=='github'){
			$result=$this->login_obj->callback();
			echo 'username：'.$result['login'].'<br />';
			echo 'id：'.$result['id'].'<br />';
			echo 'node_id：'.$result['node_id'].'<br />';
			echo '<img src="'.$result['avatar_url'].'"/>';
			exit;
		}

		if($this->login_type=='taobao'){
			$result=$this->login_obj->callback();
			echo 'taobao_user_id：'.$result['taobao_user_id'].'<br />';
			echo 'taobao_user_nick：'.$result['taobao_user_nick'].'<br />';
			echo 'taobao_open_uid：'.$result['taobao_open_uid'].'<br />';
			echo "<img src='https://wwc.alicdn.com/avatar/getAvatar.do?userId={$result['taobao_user_id']}&width=100&height=100&type=sns' />";
			exit;
		}

		if($this->login_type=='weixin'){
			$result=$this->login_obj->callback();
			print_r($result);
			exit;
		}

		
	}
}
?>