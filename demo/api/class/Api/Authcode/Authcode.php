<?php
/**
 * 短信验证码
 */
class Api_Authcode extends Api {
	protected $_checkLogin = false;

	protected $_redisZDLH;

	public function __construct() {
		parent::__construct();
		$this->_redisZDLH = Dao_Redis_Zdlh::getInstance();
	}
}