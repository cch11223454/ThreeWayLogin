<?php
		require './weibologin/WeiboLogin.php';
		$obj= new \WeiboLogin();
		$obj->app_key='2139782155';
		$obj->app_secret="1197d4c550cd398f873571ff518e85e1";
		$obj->call_back="https://www.lingfengveeker.com/index.php/Admin/Api/weibologin";

		if(empty($_REQUEST['code'])){
			$obj->get_code();
		}else{
			$access_token=$obj->get_access_token($_REQUEST['code']);

			$user_info=$obj->get_user_info($access_token);

			var_dump($user_info);

		}
?>