<?php
/**
 * OSS
 */
require_once PATH_INCLUDES . '/aliyun/oss/vendor/autoload.php';
use OSS\OssClient;

class Obj_Aliyun_Oss extends Obj_Aliyun {
	private $_ossClient = "";

	public function __construct() {
		$appEnv = WesApp::$appEnv;
		$oss = WesConfig::get("misc.oss", true);
		$endpoint = $oss["endpoint"][$appEnv];
		$this->_bucket = $oss["bucket"];
		$this->_ossClient = new OssClient($this->_accessKeyId, $this->_accessKeySecret, $endpoint, false);
	}

	public function putObject($toFile, $content, $p = "protected") {
		$res = $this->_ossClient->putObject($this->_bucket[$p], $toFile, $content);
		return $res;
	}

	public function uploadFile($toFile, $localFile, $p = "protected") {
		$res = $this->_ossClient->uploadFile($this->_bucket[$p], $toFile, $localFile);
		return $res;
	}
}