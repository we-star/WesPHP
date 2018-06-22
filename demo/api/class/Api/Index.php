<?php
/**
 * API示例
 */
class Api_Index extends Api {
	protected $_checkSign = false; // 不检查签名
	protected $_checkLogin = false; // 不检查登录

	protected function _do() {
		WesSession\Redis::start(Dao_Redis_Common::getInstance());
		// WesSession\Redis::set("age", "32");
		$user = WesSession\Redis::get("user");
		$age = WesSession\Redis::get("age");
		$this->_data = ["user" => $user, "age" => $age];
		// $this->_data = "接口返回数据，字符串，数组";
	}
}