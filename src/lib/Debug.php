<?php
/**
 * 调试
 */
class WesDebug {
	public static $debug = false; // 使用debug
	private static $_startTime = array();
	private static $_debugInfo = array();

	public static function start($key) {
		if (self::$debug) {
			$trace = self::_trace();
			self::$_startTime[$key] = microtime(true);
			if (!isset(self::$_debugInfo[$key])) self::$_debugInfo[$key] = array('file' => "{$trace['file']} {$trace['line']}");
		}
	}

	public static function end($key) {
		if (self::$debug) {
			$trace = self::_trace();
			$time = microtime(true);
			$exeTime = $time - self::$_startTime[$key];
			self::$_debugInfo[$key]['exeTime'] = $exeTime;

			if (isset(self::$_debugInfo[$key]['file']) &&  strpos(self::$_debugInfo[$key]['file'], " ~ {$trace['line']}}") === false) {
				self::$_debugInfo[$key]['file'] .= " ~ {$trace['line']}";
			}
		}

	}

	public static function setDebugInfo($key, $infoKey, $infoData) {
		if (self::$debug) {
			self::$_debugInfo[$key][$infoKey] = $infoData;
		}

	}

	public static function writeDebugInfo() {
		if (self::$_debugInfo) {
			$time = microtime(true);
			foreach(self::$_debugInfo as $key => &$debugInfo) {
				if (!isset($debugInfo['exeTime'])) {
					$debugInfo['exeTime'] = $time - self::$_startTime[$key];
				}
			}
			$log = json_encode(self::$_debugInfo);
			WesLog::debug($log);
		}
	}

	private static function _trace() {
		$trace = debug_backtrace();
		unset($trace[0]);
		$res = array('file' => $trace[1]['file'], 'line' => $trace[1]['line']);
		return $res;
	}
}
