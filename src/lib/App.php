<?php

/**
 * WesPHP2.0
 * App
 */
class WesApp {

	public static $now = 0; // 当前时间
	public static $appEnv = ""; // 应用环境
	private $_target = "target"; // 默认路由名称
	private $_index = "Index"; // 默认类

	public function __construct($target = null, $index = null) {
		self::$now = $_SERVER["REQUEST_TIME"];
		if ($target) $this->_target = $target;
		if ($index) $this->_index = $index;
		if (isset($_SERVER["APP_ENV"])) self::$appEnv = $_SERVER["APP_ENV"];
		if (defined("APP_ENV")) self::$appEnv = APP_ENV;
	}

	public function run($classType) {
		try {
			$this->_checkDefined(); // 检查必须要定义的常量
			$classType .= "_";
			$classMethod = WesVar::request($this->_target);
			if (isset($_REQUEST[$this->_target])) {
				unset($_REQUEST[$this->_target]);
			}
			$argv = array();
			if (!$classMethod) {
				if (PHP_SAPI == "cli") {
					$argv = WesVar::server("argv");
					if (!empty($argv[1])) {
						$classMethod = $classType . $argv[1];
						unset($argv[1]);
					}
					unset($argv[0]);
				} else {
					$classMethod = "{$classType}{$this->_index}";
				}
			} else {
				$classMethod = $classType . $classMethod;
			}
			if (!$classMethod)
				throw new ErrorException("target is null!", 101);
			$classMethod = trim($classMethod, "/");
			$classMethod = str_replace("/", "_", $classMethod);
			WesController::get($classMethod, $argv);
		} catch (Exception $e) {
			$this->_output($e);
		}
	}

	public function __destruct() {
		try {
			WesDebug::writeDebugInfo();
			WesLog::write();
		} catch (Exception $e) {
			$this->_output($e);
		}
	}

	private function _output($e) {
		$code = $e->getCode();
		$data["msg"] = $e->getMessage();
		$data["trace"] = $e->getTraceAsString();
		$output = array("code" => $code, "data" => $data);
		if (PHP_SAPI != "cli") {
			if (self::$appEnv == "dev") {
				header("Content-type: application/json;charset=utf-8");
				print_r($output['data']);
			} else {
				WesLog::fatal(json_encode($output));
			}
		} else {
			print_r($output['data']);
		}
	}

	private function _checkDefined() {
		if (!defined('PATH_WESPHP')) throw new ErrorException("PATH_WESPHP is not defined!", 2001);
		if (!defined('PATH_CLASS')) throw new ErrorException("PATH_CLASS is not defined!", 2001);
		if (!defined("PATH_LOG")) throw new ErrorException("PATH_LOG is not defined!", 2001);
		if (!self::$appEnv) throw new Exception("APP_ENV is not defined!", 2001);
	}

}
