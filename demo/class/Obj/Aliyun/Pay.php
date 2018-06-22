<?php
/**
 * 支付宝支付
 */
require_once PATH_INCLUDES . '/aliyun/pay/AopSdk.php';

class Obj_Aliyun_Pay {
	private $_aop;
	private $_config; // 配置

	public function __construct() {
		$this->_config = WesConfig::get("misc.alipay", true);
		$this->_aop = new AopClient;
	}

	/**
	 * APP支付
	 * @param  array $order [订单信息]
	 * @return string       [支付宝下单后返回值]
	 */
	public function app($order) {
		$appEnv = WesVar::server("APP_ENV");

		$this->_aop->gatewayUrl = $this->_config["gateway_url"];
		$this->_aop->appId = $this->_config["appid"];
		$this->_aop->rsaPrivateKey = $this->_config["merchant_private_key"];
		$this->_aop->format = "json";
		$this->_aop->charset = "UTF-8";
		$this->_aop->signType = "RSA2";
		$this->_aop->alipayrsaPublicKey = $this->_config["alipay_public_key"];

		$order["product_code"] = "QUICK_MSECURITY_PAY";
		$bizcontent = json_encode($order, JSON_UNESCAPED_UNICODE);

		$request = new AlipayTradeAppPayRequest();
		$request->setNotifyUrl($this->_config["notify_url"][$appEnv]);
		$request->setBizContent($bizcontent);
		$response = $this->_aop->sdkExecute($request);
		return $response;
	}

	/**
	 * 提现转帐
	 * @param  string $buyerUid [支付宝用户ID]
	 * @param  float  $money    [金额]
	 * @return mixed            [返回]
	 */
	public function transfer($buyerUid, $money) {
		$bizNo = date('Ymd') . substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
		$params = ["out_biz_no" => $bizNo, "payee_type" => "ALIPAY_USERID", "payee_account" => $buyerUid, "amount" => (string)round($money, 2)];

		$this->_aop->gatewayUrl = $this->_config["gateway_url"];
		$this->_aop->appId = $this->_config["appid"];
		$this->_aop->rsaPrivateKey = $this->_config["merchant_private_key"];
		$this->_aop->format = "json";
		$this->_aop->charset = "UTF-8";
		$this->_aop->signType = "RSA2";
		$this->_aop->alipayrsaPublicKey = $this->_config["alipay_public_key"];

		$bizcontent = json_encode($params, JSON_UNESCAPED_UNICODE);
		$request = new AlipayFundTransToaccountTransferRequest();
		$request->setBizContent($bizcontent);
		$res = $this->_aop->execute($request);

		if (isset($res->alipay_fund_trans_toaccount_transfer_response) && $res->alipay_fund_trans_toaccount_transfer_response->code == "10000") {
			return true;
		}
		return false;
	}

	/**
	 * 支付成功后异步通，检查签名
	 * @param  array $post 	 [支付宝POST值]
	 * @return boolean       [返回值]
	 */
	public function checkSign($post) {
		$this->_aop->alipayrsaPublicKey = $this->_config["alipay_public_key"];
		$flag = $this->_aop->rsaCheckV1($post, NULL, "RSA2");
		return $flag;
	}
}