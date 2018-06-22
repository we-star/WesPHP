<?php
/**
 * WesPHP2.0
 * Session
 */
class WesSession {
	private static $_session = null;

	public static function start($saveType = "file") {
		if (!self::$_session) {
			$saveType = ucfirst($saveType);
			$class = "WesSession{$saveType}";
			self::$_session = new $class;
			session_set_save_handler(self::$_session, true);
			session_start();
		}
	}

	public static function get($key) {
		if (!self::$_session) {
			self::$_session = true;
			session_start();
			// throw new ErrorException("session handler is empty! call WesSession::start()", "3001");
		}
		return WesVar::session($key);
	}

	public static function set($key, $val = null) {
		if (!self::$_session) {
			self::$_session = true;
			session_start();
			// throw new ErrorException("session handler is empty! call WesSession::start()", "3001");
		}
		if ($val !== null) {
			$_SESSION[$key] = $val;
		} else {
			unset($_SESSION[$key]);
		}
	}

	public static function del() {
		session_destroy();
	}
}
