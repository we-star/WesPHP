<?php
/**
 * AES
 */
namespace WesCrypt;
use WesCrypt\AES;

class AES {
	private $_key = ""; // 私钥

	// PHP7.1.0以上环境需要安装mcrypt扩展, 旧方法
	/*
	private $_cipher; // 算法名称 MCRYPT_RIJNDAEL_128 MCRYPT_RIJNDAEL_192 MCRYPT_RIJNDAEL_256
	private $_mode; // 模式 "ecb"，"cbc"，"cfb"，"ofb"，"nofb", "stream"

	public function __construct($key, $iv = "", $cipher = MCRYPT_RIJNDAEL_128, $mode = MCRYPT_MODE_CBC) {
		$this->_key = $key;
		$this->_cipher = $cipher;
		$this->_mode = $mode;
		$this->_iv = $iv ? $iv : $key;
	}

	public function encode($data) {
        $module = mcrypt_module_open($this->_cipher, '', $this->_mode, $this->_iv);
        mcrypt_generic_init($module, $this->_key, $this->_iv);
        $block = mcrypt_get_block_size($this->_cipher, $this->_mode);
        $pad = $block - (strlen($encryptStr) % $block);
        $encryptStr .= str_repeat(chr($pad), $pad);

        $encrypted = mcrypt_generic($module, $encryptStr);

        mcrypt_generic_deinit($module);
        mcrypt_module_close($module);

        return base64_encode($encrypted);
	}

	public function decode($data) {
        $module = mcrypt_module_open($this->_cipher, '', $this->_mode, $this->_iv);

        mcrypt_generic_init($module, $this->_key, $this->_key);

        $data = base64_decode($data);
        $str = mdecrypt_generic($module, $data);
        return $this->_trimEnd($str);
	}

	// 去尾
	private function _trimEnd($str) {
		$len = strlen($str);
		$c = $str[$len - 1];

		if (ord($c) < $len) {
			for($i = $len - ord($c); $i < $len; $i ++) {
				if($str[$i] != $c) {
					return $str;
				}
			}
			return substr($str, 0, $len - ord($c));
		}
		return $str;
	}
	*/

	// PHP7.1.0 版本使用
	public function __construct($key, $iv = "", $method = "AES-128-CBC") {
		$this->_key = $key;
		$this->_iv = $iv ? $iv : $key;
		$this->_method = $method;
	}

	public function encode($data) {
		$encrypted = \openssl_encrypt($data, $this->_method, $this->_key, OPENSSL_RAW_DATA, $this->_iv);
		// return base64_encode($encrypted);
		return \WesString::base64Encode($encrypted);
	}

	public function decode($data) {
		// $encrypted = base64_decode($data);
		$encrypted = \WesString::base64Decode($data);
		$decrypted = \openssl_decrypt($encrypted, $this->_method, $this->_key, OPENSSL_RAW_DATA, $this->_iv);
		return $decrypted;
	}
}