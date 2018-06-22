<?php
/**
 * WesPHP2.0
 * Log
 */
class WesLog {
	private static $_logs = array();

	public static function info($log) {
		self::_setLog("info", $log);
	}

	public static function error($log){
		self::_setLog('error', $log);
	}

	public static function notice($log){
		self::_setLog('notice', $log);
	}

	public static function warning($log){
		self::_setLog('warning', $log);
	}

	public static function fatal($log){
		self::_setLog('fatal', $log);
	}

	public static function debug($log) {
		self::_setLog('debug', $log);
	}

	public static function write() {
		if (self::$_logs) {
			$dateH = date("YmdH");
			foreach (self::$_logs as $type => $logs) {
				$file = PATH_LOG . "/{$type}/{$dateH}.log";
				if ($logs) $log = join('', $logs);
				WesFile::write($file, $log, 'a');
			}
		}
	}

	private static function _setLog($key, $log) {
		if ($log) {
			$dateTime = date("Y-m-d H:i:s");
			self::$_logs[$key][] = '"' . $dateTime . '" "' . $log . '"' . "\n";
		}
	}
}
