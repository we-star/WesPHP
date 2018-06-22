<?php
/**
 * WesPHP2.0
 * Curl
 */
class WesCurl {
	private static $_timeout = 2;
	private static $_method  = 'GET';
	private static $_header  = array();
	private static $_options = array();
	private static $_cookies = array();
	private static $_multiUrl= array();
	private static $_data    = '';
	private static $_url     = '';

	public static function setData($data, $method = 'GET') {
		if ($data) {
			self::$_method = strtolower($method);
			if (is_array($data) && self::$_method == "get") {
				self::$_data = http_build_query($data);
			} else {
				self::$_data = $data;
			}
		}
	}

	public static function setOption($key, $val = null) {
		if ($key) {
			if (is_array($key)) {
				self::$_options = $key;
			} else {
				if (!is_null($val)) self::$_options[$key] = $val;
			}
		}
	}

	public static function setCookie($key, $val) {
		if ($key && $val) {
			self::$_cookies[] = "{$key}={$val}";
		}
	}

	public static function setUserPass($user, $password) {
		self::$_options[CURLOPT_USERPWD] = "{$user}:{$password}";
	}

	public static function call($urls, $timeout = 2, $header = array()){
		self::$_header = $header;
		self::$_timeout = $timeout;

		if (!is_array($urls)) {
			return self::_call($urls);
		} else {
			return self::_multiCall($urls);
		}
	}

	// 非阻塞调用
	public static function callWithUnblock($urls) {
		if (is_array($urls)) {
			$multiInit = curl_multi_init();
			foreach($urls as $key => $urlInfo) {
				$data = !empty($urlInfo['data']) ? $urlInfo['data'] : array();
				$method = !empty($urlInfo['method']) ? $urlInfo['method'] : 'GET';
				self::setData($data, $method);
				if (!empty($urlInfo['cookie']) && is_array($urlInfo['cookie'])) {
					foreach ($urlInfo['cookie'] as $cookieKey => $value) {
						self::setCookie($cookieKey, $value);
					}
				}
				if (!empty($urlInfo['userPass'])) self::setUserPass($urlInfo['userPass']['user'], $urlInfo['userPass']['pass']);
				$ch = self::_initCurl($urlInfo['url'], true, $key);
				curl_multi_add_handle($multiInit, $ch);
			}
			do {
				curl_multi_exec($multiInit, $running);
			} while ($running);
		}
	}

	private static function _call($url) {
		$ch = self::_initCurl($url);
		$content = curl_exec($ch);
		$errno = curl_errno($ch);
		$error = curl_error($ch);
		if ($errno) {
			WesLog::warning(self::$_url . "\terrno:{$errno}, error:{$error}");
		}
		curl_close($ch);
		return $content;
	}

	private static function _multiCall($urls) {
		$contents = $chs = array();
		if ($urls) {
			$multiInit = curl_multi_init();
			foreach($urls as $key => $urlInfo) {
				$data = !empty($urlInfo['data']) ? $urlInfo['data'] : array();
				$method = !empty($urlInfo['method']) ? $urlInfo['method'] : 'GET';
				self::setData($data, $method);
				if (!empty($urlInfo['cookie']) && is_array($urlInfo['cookie'])) {
					foreach ($urlInfo['cookie'] as $cookieKey => $value) {
						self::setCookie($cookieKey, $value);
					}
				}
				if (!empty($urlInfo['userPass'])) self::setUserPass($urlInfo['userPass']['user'], $urlInfo['userPass']['pass']);
				$ch = self::_initCurl($urlInfo['url'], true, $key);
				$chs[$key] = $ch;
				curl_multi_add_handle($multiInit, $ch);
			}
			do {
				curl_multi_exec($multiInit, $running);
			} while ($running);
			while ($done = curl_multi_info_read($multiInit)) {
				$chandle = $done['handle'];
				foreach($chs as $key => $ch) {
					if ($chandle == $ch) {
						break;
					}
				}
				$errno = $done['result'];
				$error = curl_error($ch);
				if ($errno) {
					$content = "{'errno':{$errno}, 'error':'{$error}'}";
					WesLog::warning(self::$_multiUrl[$key] . "\t{$content}");
				} else {
					$content = curl_multi_getcontent($ch);
					curl_multi_remove_handle($multiInit, $ch);
					curl_close($ch);
				}
				$contents[$key] = $content;
			}
			curl_multi_close($multiInit);
		}
		return $contents;
	}

	private static function _initCurl($url, $multi = false, $key = null) {
		$urlScheme = parse_url($url);
		if (self::$_data && self::$_method == "get") {
			if (false !== strpos($url, '?')) {
				$url .= "&" . self::$_data;
			} else {
				$url .= "?" . self::$_data;
			}
		}
		if ($multi && $key) self::$_multiUrl[$key] = $url;
		self::$_url = $url;
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLINFO_HEADER_OUT, true);
		if (defined('CURLOPT_TIMEOUT_MS')) {
			$timeout = self::$_timeout * 1000;
			curl_setopt($ch, CURLOPT_NOSIGNAL, 1);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT_MS, $timeout);
			curl_setopt($ch, CURLOPT_TIMEOUT_MS, $timeout);
		} else {
			$timeout = self::$_timeout;
			if ($timeout < 1) $timeout = 1;
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
			curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
		}
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		if (self::$_header) curl_setopt($ch, CURLOPT_HTTPHEADER, self::$_header);
		if (isset($urlScheme["scheme"]) && $urlScheme["scheme"] == 'https') {
			curl_setopt($ch, CURLOPT_VERBOSE, 1);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		}
		if (self::$_method == 'post') {
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, self::$_data);
		}
		if (self::$_options) {
			foreach (self::$_options as $key => $value) {
				if ($value) curl_setopt($ch, $key, $value);
			}
		}
		if (self::$_cookies) {
			curl_setopt($ch, CURLOPT_COOKIE, join(';', self::$_cookies));
		}
		self::$_data = '';
		self::$_options = array();
		self::$_cookies = array();
		return $ch;
	}
}
