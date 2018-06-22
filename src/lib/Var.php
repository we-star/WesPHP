<?php
/**
 * WesPHP2.0
 * Var
 */
class WesVar {
	public static function get($key, $default = null, $quotes_gpc = false) {
		return self::_get("get", $key, $default, $quotes_gpc);
	}

	public static function getx() {
		$keys = func_get_args();
		$quotes_gpc = end($keys);
		if ($quotes_gpc !== true) {
			$quotes_gpc = false;
		} else {
			array_pop($keys);
		}
		return self::_getx('get', $keys, $quotes_gpc);
	}

	public static function post($key, $default = null, $quotes_gpc = false){
		return self::_get('post', $key, $default, $quotes_gpc);
	}

	public static function postx() {
		$keys = func_get_args();
		$quotes_gpc = end($keys);
		if ($quotes_gpc !== true) {
			$quotes_gpc = false;
		} else {
			array_pop($keys);
		}
		return self::_getx('post', $keys, $quotes_gpc);
	}

	public static function request($key, $default = null, $quotes_gpc = false){
		return self::_get('request', $key, $default, $quotes_gpc);
	}

	public static function requestx() {
		$keys = func_get_args();
		$quotes_gpc = end($keys);
		if ($quotes_gpc !== true) {
			$quotes_gpc = false;
		} else {
			array_pop($keys);
		}
		return self::_getx('request', $keys, $quotes_gpc);
	}

	public static function server($key, $default = null, $quotes_gpc = false){
		return self::_get('server', $key, $default, $quotes_gpc);
	}

	public static function serverx() {
		$keys = func_get_args();
		$quotes_gpc = end($keys);
		if ($quotes_gpc !== true) {
			$quotes_gpc = false;
		} else {
			array_pop($keys);
		}
		return self::_getx('server', $keys, $quotes_gpc);
	}

	public static function cookie($key, $default = null, $quotes_gpc = false){
		return self::_get('cookie', $key, $default, $quotes_gpc);
	}

	public static function cookiex() {
		$keys = func_get_args();
		$quotes_gpc = end($keys);
		if ($quotes_gpc !== true) {
			$quotes_gpc = false;
		} else {
			array_pop($keys);
		}
		return self::_getx('cookie', $keys, $quotes_gpc);
	}

	public static function session($key, $default = null, $quotes_gpc = false){
		return self::_get('session', $key, $default, $quotes_gpc);
	}

	public static function sessionx() {
		$keys = func_get_args();
		$quotes_gpc = end($keys);
		if ($quotes_gpc !== true) {
			$quotes_gpc = false;
		} else {
			array_pop($keys);
		}
		return self::_getx('session', $keys, $quotes_gpc);
	}

	public static function file($key) {
		return self::_get('file', $key, null, false);
	}

	public static function filex() {
		$keys = func_get_args();
		return self::_getx('file', $keys, false);
	}

	private static function _get($type, $key, $default, $quotes_gpc) {
		$data = self::_getValue($type);
		if (!isset($data[$key]) && isset($default)) return $default;
		if (isset($data[$key])) {
			if ($quotes_gpc) {
				if (is_array($data[$key])) {
					foreach($data[$key] as &$val) {
						$val = addslashes($val);
					}
				} else {
					$data[$key] = addslashes($data[$key]);
				}
			}
			return $data[$key];
		}
		return null;
	}

	private static function _getx($type, $keys, $quotes_gpc) {
		$data = self::_getValue($type);
		if ($keys && is_array($keys)) {
			$values = array();
			foreach ($keys as $key) {
				if (isset($data[$key])) {
					$values[$key] = $quotes_gpc ? addslashes($data[$key]) : $data[$key];
				}
			}
			return $values;
		}
		return $data;
	}

	private static function _getValue($type) {
		$data = array();
		if ($type == 'get') $data = $_GET;
		if ($type == 'post') $data = $_POST;
		if ($type == 'request') $data = $_REQUEST;
		if ($type == 'server') $data = $_SERVER;
		if ($type == 'cookie') $data = $_COOKIE;
		if ($type == 'session') $data = $_SESSION;
		if ($type == 'file') $data = $_FILES;
		return $data;
	}
}
