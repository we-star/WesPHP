<?php
/**
 * API基类
 */
abstract class Api extends WesObj {
	protected $_code = 0; // 代码 0表示正确，非0表示错误
	protected $_msg = "ok"; // 消息
	protected $_data = array(); // 数据

	protected $_checkLogin = true;
	protected $_checkSign = true;

	protected $_params = array(); // 所有参数
	protected $_userInfo = array(); // 用户信息

	public function __construct() {
	}

	public function run() {
		$this->_checkSign(); // 检查签名
		$this->_checkParams(); // 检查参数
		$this->_checkLogin(); // 检查登录
		$this->_do(); // 执行程序
		$this->_json();
	}

	protected function _do() {
		$this->_data = "hello api";
	}

	/**
	 * 设置错误码，消息和数据。在子类调用该方法直接返回
	 */
	protected function _setCode($code, $msg = "", $data = []) {
		$this->_code = $code;
		$this->_msg = $msg;
		$this->_json();
	}

	/**
	 * 以JSON结构输出
	 */
	protected function _json($data = null) {
		header("Access-Control-Allow-Origin: *");
		header("Content-type: application/json; charset=utf-8");
		if ($data === null) {
			$res = array("code" => $this->_code, "msg" => $this->_msg, "data" => $this->_data);
		} else {
			$res = $data;
		}
		echo json_encode($res, JSON_UNESCAPED_UNICODE);
		exit;
	}

	// 默认不检查参数合法性，如果需要请在子类重写该方法
	protected function _checkParams() {

	}

	// 默认需要检查数字签名
	protected function _checkSign() {
		$this->_params = WesVar::getx();
		unset($this->_params["target"]);
		if ($this->_checkSign) {
			$post = WesVar::postx();
			if ($post) {
				unset($this->_params["params"]);
				unset($this->_params["signinfo"]);
				$params = WesVar::post("params");
				$signinfo = WesVar::post("signinfo");
				if (!$params) $this->_setCode(100000, "缺少参数或参数为空：params");
				if (!$signinfo) $this->_setCode(100000, "缺少参数或参数为空：signinfo");

				$key = substr($signinfo, 0, 16);
				$aes = new WesCrypt\AES($key, $key);
				$str = $aes->decode($params);
				$params = json_decode($str, true);
				if (!$params) $this->_setCode(100001, "签名失败");

				foreach ($params as $key => $value) {
					$_REQUEST[$key] = $value;
					$this->_params[$key] = $value;
				}
			}
		}
	}

	// 默认需要验证登录
	protected function _checkLogin() {
		if ($this->_checkLogin && empty($this->_params["token"])) $this->_setCode(100000, "缺少参数或参数为空：token，您需要先登录");

		if (!empty($this->_params["token"])) {
			$aesConf = WesConfig::get("misc.AES");
			$aes = new WesCrypt\AES($aesConf["key"], $aesConf["iv"]);
			$token = $aes->decode($this->_params["token"]);

			$userInfo = json_decode($token, true);
			if (!$userInfo) $this->_setCode(110003, "token不正确，请重新登录");

			$this->_userInfo = $userInfo;
		} else {
			$this->_userInfo = ["uid" => "", "mobile" => "", "time" => WesApp::$now];
		}
	}
}