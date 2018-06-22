<?php
/**
 * RSA
 */

namespace WesCrypt;
use WesCrypt\RSA;

class RSA {
	private $_privateKey;
	private $_publicKey;

	public function __construct() {
		if (!function_exists("openssl_pkey_new") || !extension_loaded("openssl")) {
			throw new \ErrorException("do ont support openssl!", 1001);
		}
	}

	public function createKey($bits = 1024) {
		$config = array(
			"private_key_bits" => $bits,
			"private_key_type" => OPENSSL_KEYTYPE_RSA
		);
		$rsa = openssl_pkey_new($config);
		if(!$rsa) {
			$error = openssl_error_string();
			throw new \Exception($error, 2000);
		}

		openssl_pkey_export($rsa, $this->_privateKey, null, $config);
		$publicKeyArr = openssl_pkey_get_details($rsa);
		$this->_publicKey = $publicKeyArr["key"];
		openssl_pkey_free($rsa);
	}

	public function getKeys() {
		return array("privateKey" => $this->_privateKey, "publicKey" => $this->_publicKey);
	}

	public function setPrivateKey($privateKey) {
		$this->_privateKey = $privateKey;
	}

	public function setPublicKey($publicKey) {
		$this->_publicKey = $publicKey;
	}

	public function privateEncrypt($data, $padding = OPENSSL_PKCS1_PADDING) {
		$resData = "";
		openssl_private_encrypt($data, $resData, $this->_privateKey, $padding);
		return $resData;
	}

	public function privateDecrypt($data,$padding = OPENSSL_PKCS1_PADDING) {
		$resData = "";
		openssl_private_decrypt($data, $resData, $this->_privateKey, $padding);
		return $resData;
	}

	public function publicEncrypt($data, $padding = OPENSSL_PKCS1_PADDING) {
		$resData = "";
		openssl_public_encrypt($data, $resData, $this->_publicKey, $padding);
		return $resData;
	}

	public function publicDecrypt($data, $padding = OPENSSL_PKCS1_PADDING) {
		$resData = "";
		openssl_public_decrypt($data, $resData, $this->_publicKey, $padding);
		return $resData;
	}
}
