<?php
/**
 * 发送验证码
 */
class Api_Authcode_send extends Api_Authcode {
	private $_mobile;
	private $_smscode;

	protected function _do() {
		$mobile = WesVar::get("mobile");
		$smscode = WesVar::get("smscode");

		$key = "code_{$mobile}";

		$code = $this->_redisZDLH->get($key);
		if (!$code) {
			$code = rand(1000, 9999);
			$this->_redisZDLH->set("code_{$mobile}", $code, 300);
		}

		$sms = WesController::get("Obj_Aliyun_Sms");
		$res = $sms->send($mobile, $smscode, array("code" => $code));
		if (!isset($res->Code) || $res->Code != "OK") {
			$this->_setCode(10001, "验证码发送失败，请重试。{$res->Message}");
		}
		$this->_data = strtolower($res->Code);
	}

	protected function _checkParams() {
		$this->_mobile = WesVar::get("mobile");
		if (!$this->_mobile) $this->_setCode(1000, "缺少参数：mobile");
		$this->_smscode = WesVar::get("smscode");
		if (!$this->_smscode) $this->_setCode(1000, "缺少参数：smscode");
	}
}