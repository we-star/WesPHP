<?php
/**
 * WesPHP2.0
 * Config
 */
class WesConfig {
	private static $_data = array();

	/**
	 * 获取配置
	 * @param  [type] $key        [健值]
	 * @param  [type] $commonPath [是否使用通用路径]
	 * @return [type] $value      [返回值]
	 */
	public static function get($key, $commonPath = null){
		if (!defined("PATH_CONFIG")) throw new ErrorException("PATH_CONFIG is not defined!", 1001);
		$configPath = PATH_CONFIG;
		if ($commonPath) {
			$configPath = PATH_COMMON_CONFIG;
		}
		if (strpos($key, '.') !== false) {
			$keyArr = explode('.', $key);
			$dir = str_replace('_', DIRECTORY_SEPARATOR, $keyArr[0]);
			$key = str_replace("{$keyArr[0]}.", '', $key);
		} else {
			$dir = str_replace('_', DIRECTORY_SEPARATOR, $key);
			$key = null;
		}

		$dirKey = md5($configPath . $dir);

		if (!isset(self::$_data[$dirKey])) {
			if (!$commonPath) {
				$file = PATH_CONFIG . DIRECTORY_SEPARATOR . "{$dir}.conf.php";
			} else {
				if (!defined("PATH_COMMON_CONFIG")) throw new ErrorException("PATH_COMMON_CONFIG is not defined!", 1001);
				$file = PATH_COMMON_CONFIG . DIRECTORY_SEPARATOR . "{$dir}.conf.php";
			}
			if (!file_exists($file)) throw new ErrorException("{$file} not exists!", 10002);
			self::$_data[$dirKey] = require_once $file;
		}

		$value = self::_get(self::$_data[$dirKey], $key);
		return $value;
	}

	private static function _get($data, $key) {
		if ($key) {
			if (strpos($key, '.') !== false) {
				$val = null;
				$keyArr = explode('.', $key);
				$data = $data[$keyArr[0]];
				$newKey = str_replace("{$keyArr[0]}.", '', $key);
				if (strpos($newKey, '.') === false) {
					$data = $data[$newKey];
				} else {
					$data = self::_get($data, $newKey);
				}
			} else {
				if (empty($data[$key])) {
					$data = null;
				} else {
					$data = $data[$key];
				}
			}
		}
		return $data;
	}
}
