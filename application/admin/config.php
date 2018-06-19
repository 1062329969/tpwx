<?php
 return [
	'sitename'=>'雍享汇',
	'domain'=>'http://ihair.yongxianghui.net',
	'Appid'=>'wxa7704589f3ea416d',
	'Appsecret'=>'5aa272ed957a388543431724afcd178a',
	'picType'=>'',
	'picSize'=>'',
	'EBusinessID'=>'1293292',
	'AppKey'=>'ee62531a-ea57-43a2-91c1-16ef6831dfb9',
	 'session'                => [
		 'id'             => '',
		 // SESSION_ID的提交变量,解决flash上传跨域
		 'var_session_id' => '',
		 // SESSION 前缀
		 'prefix'         => 'think',
		 // 驱动方式 支持redis memcache memcached
		 'type'           => '',
		 // 是否自动开启 SESSION
		 'auto_start'     => true,
		 'expire' => 1800,
		 'lifetime'=>true
	 ],
];
?>
