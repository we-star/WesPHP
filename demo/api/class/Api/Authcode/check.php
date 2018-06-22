<?php
/**
 * 校验验证码
 */
class Api_Authcode_check extends Api_Authcode {
	private $_mobile;
	private $_authcode;

	protected function _do() {
		$key = "code_{$mobile}";
		$authcode = $this->_redisZDLH->get($key);
		if (!$authcode) $this->_setCode(10001, "验证码已经失效，请重新获取");
		if ($authcode != $this->_authcode) $this->_setCode(10001, "验证码不正确，请重试");
	}

	protected function _checkParams() {
		$this->_mobile = WesVar::get("mobile");
		if (!$this->_mobile) $this->_setCode(1000, "缺少参数：mobile");
		$this->_authcode = WesVar::get("authcode");
		if (!$this->_authcode) $this->_setCode(1000, "缺少参数：authcode");
	}
}