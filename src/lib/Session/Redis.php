<?php
/**
 * Redis形式存储session
 */
namespace WesSession;
use WesSession\Redis;

class Redis implements \SessionHandlerInterface {
	private static $_session = null;
	private static $_dao = null;

	public static function start($dao) {
		if (!self::$_session && !self::$_dao) {
			if (!$dao) throw new \ErrorException("\$dao can not be empty!", 3001);
			self::$_dao = $dao;
			self::$_session = new self();
			session_set_save_handler(self::$_session, true);
			session_start();
		}
	}

	public static function get($key, $val = null) {
		if (!self::$_session || !self::$_dao) throw new \ErrorException("you must call WesSession\Redis::start($dao) first!", 3000);
		$session = null;
		if (isset($_SESSION[$key])) {
			$session = $_SESSION[$key];
		}
		if (!$session && $val) {
			$session = $val;
		}
		return $session;
	}

	public static function set($key, $val = null) {
		if (!self::$_session || !self::$_dao) throw new \ErrorException("you must call WesSession\Redis::start($dao) first!", 3000);
		if ($val !== null) {
			$_SESSION[$key] = $val;
		} else {
			unset($_SESSION[$key]);
		}
	}

	public static function del() {
		if (!self::$_session || !self::$_dao) throw new \ErrorException("you must call WesSession\Redis::start($dao) first!", 3000);
		session_destroy();
	}

	public function open($savePath, $sessionName) {
		return true;
	}

	public function close() {
		$id = session_id();
		$key = "sess_{$id}";
		$lifeTime = ini_get("session.gc_maxlifetime");
		self::$_dao->setTimeout($key, $lifeTime);
		return true;
	}

	public function read($id) {
		$key = "sess_{$id}";
		$data = self::$_dao->get($key);
		return $data;
	}

	public function write($id, $data) {
		if ($data) {
			$lifeTime = ini_get("session.gc_maxlifetime");
			self::$_dao->setex("sess_{$id}", $lifeTime, $data);
		}
		return true;
	}

	public function gc($lifeTime) {
		return true;
	}

	public function destroy($id) {
		self::$_dao->delete("sess_{$id}");
		return true;
	}

	private function __construct() {
	}
}
