<?php
/**
 * 文件形式存储session
 */
namespace WesSession;
use WesSession\File;

class File implements \SessionHandlerInterface {
	private static $_session = null;
	private $_sessionPath;

	public static function get($key, $val = null) {
		self::_start();
		if (isset($_SESSION[$key])) {
			$session = $_SESSION[$key];
		}
		if (!$session && $val) {
			$session = $val;
		}
		return $session;
	}

	public static function set($key, $val = null) {
		self::_start();
		if ($val !== null) {
			$_SESSION[$key] = $val;
		} else {
			unset($_SESSION[$key]);
		}
	}

	public static function del() {
		self::_start();
		session_destroy();
	}

	private static function _start() {
		if (!self::$_session) {
			self::$_session = new self();
			session_set_save_handler(self::$_session, true);
			session_start();
		}
	}

	public function open($savePath, $sessionName) {
		if (!defined("PATH_SESSION")) throw new \ErrorException("'PATH_SESSION' is not defined!", 2001);
		$this->_sessionPath = PATH_SESSION;
		$id = session_id();
		$dir = substr(md5($id), 10, 3);
		$this->_sessionPath .= "/{$dir}";
		return true;
	}

	public function close() {
		return true;
	}

	public function read($id) {
		$data = "";
		$file = "{$this->_sessionPath}/sess_{$id}";
		if (file_exists($file)) {
			$lifeTime = ini_get("session.gc_maxlifetime");
			$fileTime = filemtime($file);
			if ($fileTime + $lifeTime > \WesApp::$now) {
				touch($file);
				$data = \WesFile::read($file);
			}
		}
		return $data;
	}

	public function write($id, $data) {
		\WesFile::write("{$this->_sessionPath}/sess_{$id}", $data);
		return true;
	}

	public function gc($lifeTime) {
		$now = \WesApp::$now;
		$files = \WesFile::read($this->_sessionPath);
		if ($files) {
			foreach($files as $file) {
				$fileTime = filemtime($file);
				if ($fileTime + $lifeTime < $now) {
					\WesFile::del($file);
				}
			}
		}
		return true;
	}

	public function destroy($id) {
		\WesFile::del("{$this->_sessionPath}/sess_{$id}");
	}

	private function __construct() {
	}
}
